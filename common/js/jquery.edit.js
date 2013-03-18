/*
 * 
 */

(function($) {
	$.fn.edit = function(options, key, value) {
		// valori di default
		var config = {
			'edit' : null, // function | url | null
			'element' : 'td',
			'main' : function() {
				$this = $(this);
				$row = $this.parent().parent();
				config = $this.data();
				if (config.open) {
					$td = $row.find(config.element).eq(1).children();
					val = $td.eq(0).hide().val();
					$td.eq(1).show().text(val);
					data = {
						name : $row.find(config.element).eq(0).text(),
						value : val
					};
					if (val != config.prev) {
						config.prev=val;
						switch (typeof (config.edit)) {
						case 'string':
							if (config.ajax) {
								config.ajax(config.edit, 'post', data);
							} else
								$.ajax({
									url : config.edit,
									type : 'post',
									data : data
								});
							break;
						case 'function':
							config.edit();
							break
						default:
						}
					}
					$this.button('option', 'icons', {
						primary : "ui-icon-wrench"
					});
				} else {
					$td = $row.find(config.element).eq(1).children();
					$td.eq(0).show();
					$td.eq(1).hide();
					$this.button('option', 'icons', {
						primary : "ui-icon-check"
					});
				}
				if (config.click) {
					config.click(this);
				}
				config.open = !config.open;
				$this.data(config);
			},
			'ajax' : false, // function (url,type,data)
			'click' : false,
			'open' : false
		};
		if (!options)
			options = config;
		if (typeof (options) != 'string')
			$.extend(config, options);
		if (options == 'option') {
			if (value)
				return $(this).data(key, value);
			else
				return $(this).data(key);
		}
		this.each(function() {
			$this = $(this);
			if (typeof (options) == 'object') { // setup timer
				$this.unbind('click').click(config.main);
				$row = $this.parent().parent();
				val = $row.find(config.element).eq(1).text();
				config.prev=val;
				$this.data(config);
				$row.find(config.element).eq(1).html(
						'<input ' + (config.open ? '' : 'style="display:none"')
								+ ' value="' + val + '"/><span '
								+ (config.open ? 'style="display:none"' : '')
								+ '>' + val + '</span>');
			} else {
				// method start,stop,reset
				switch (options) {
				case 'destroy':
					$this.removeData();
					$this.unbind('click');// @todo reinplement
				}
			}
		});
		return this;
	};
})(jQuery);
