(function($) {
	jQuery.interval={
		id:0,
		el:[],
		start:function() {
			if (!this.id)
				this.id=setInterval(this.callback,1000);
		},
		stop:function() {
			clearInterval(this.id);
			this.id=0;
		},
		callback:function() {
//console.log('call');
			if (jQuery.interval.el.length) {
				temp=[];
				run=0;
				for (i in jQuery.interval.el) {
					v=jQuery.interval.el[i];
					data=v.data();
					if (data) {
						temp.push(v);
						if (data.run) {
							run++
							up=true;
							if (data.direction=='up') data.time++; else {data.time--;up=false;}
							date=new Date(data.time*1000);
							if ((data.time==0) && !up) {data.run=false;run--}
							v.data(data).text($.dateFormat(date,data.format));
							if (data.time==0) v.trigger('zero');
							if (data.time==data.target) v.trigger('alarm');	
						}
					}
				}
				jQuery.interval.el=temp;
				if (run<=0) jQuery.interval.stop();
			}
			else
				jQuery.interval.stop();
		}
	};
	jQuery.dateFormat=function(date,format) {
		switch (format) {
			case 'time': 
				s=date.getUTCSeconds();
				m=date.getUTCMinutes();
				h=date.getUTCHours();
				text=(h<10?'0'+h:h)+':'+(m<10?'0'+m:m)+':'+(s<10?'0'+s:s);
				break;
			case 'date':
				d=date.getUTCDate();
				m=date.getUTCMonth();
				y=date.getUTCFullYear();
				text=(d<9?'0'+d:d)+'/'+(m<9?'0'+m:m)+'/'+y;
				break;
			default:text=date.toString();
		}
		return text;
	}
    	$.fn.timer = function(options,key,value) {
		// valori di default
       		var config = {
       			'direction' : 'up',
       			'zero' : function(event){},
			'alarm' : function(event) {},
			'target' : 0,
			'run' : true,
			'format':'time'//time date full
		};
		if (!options) options=config;
		v=[]
       		if (typeof(options)!='string') $.extend(config, options);
		if (options=='option') {
			if (value)
			return $(this).data(key,value);
			else 
				return $(this).data(key);
		}
	        this.each(function() {
	        	$this = $(this);
       			if (typeof(options)=='object') { //setup timer
               			$this.bind('zero',config.zero);
               			$this.bind('alarm',config.alarm);
               			time=parseInt($this.text());
               			config.time=time;
               			config.reset=time;
               			$this.data(config);
				$.interval.el.push($this);
				$this.text($.dateFormat(new Date(time*1000),config.format));
			}
       			else {
       				//method start,stop,reset
       				switch (options) {
       					case 'stop' : $this.data('run',false);
       					break;
       					case 'reset' : $this.data('time',$this.data('reset'));
       					break;
       					case 'start' : $this.data('run',true);$.interval.start();
       					break;
					case 'destroy' : $this.removeData().html(' ').hide();
					break;
					
       				}
       			}
       		});
		if (config.run) $.interval.start();
		return this;
	};
})(jQuery);
