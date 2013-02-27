(function($) {
    $.fn.timer = function(options) {
        // valori di default
        var config = {
            'direction' : 'up',
            'zero' : function(event){},
            'alarm' : function() {},
            'target' : 0,
            'run' : true
        };
        if (typeof(options)=='object') $.extend(config, options);
        this.each(function() {
        	$this = $(this);
        	if (typeof(options)=='object') { //setup timer
                $this.bind('zero',config.zero);
                $this.bind('alarm',config.alarm);
                time=parseInt($this.text());
                config.time=time;
                config.reset=time;
                $this.data(config);
                //add interval
                
        	}
        	else {
        		//method start,stop,reset
        		switch (options) {
        			case 'stop' : 
        				break;
        			case 'reset' : break;
        			case 'start' : break;
        		}
        	}
        });
        
        return this;
    };
})(jQuery);
