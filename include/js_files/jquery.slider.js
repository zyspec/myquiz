

jQuery.fn.accessNews = function(settings) {
    settings = jQuery.extend({
        newsHeadline: "Top Stories",
        newsSpeed: "normal"
    }, settings);
    return this.each(function(i) {
        aNewsSlider.itemWidth = parseInt(jQuery(".item:eq(" + i + ")",".news_slider").css("width")) + parseInt(jQuery(".item:eq(" + i + ")",".news_slider").css("margin-right"));
        aNewsSlider.init(settings,this);
        jQuery(".view_all > a", this).click(function() {
            aNewsSlider.vAll(settings,this);
            return false;
        });
    });
};
var aNewsSlider = {
    itemWidth: 0,
    init: function(s,p) {
        jQuery(".messaging",p).css("display","none");
        itemLength = jQuery(".item",p).length;
        if (jQuery(".view_all",p).width() == null) {
            jQuery(".news_items",p).prepend("<p class='view_all'>" + "[" + (itemLength - 2) + " " +(rew) + "]<a href='#'></a></p>");
        }
        newsContainerWidth = itemLength * aNewsSlider.itemWidth;
        jQuery(".container",p).css("width",newsContainerWidth + "px");
        jQuery(".next",p).css("display","block");
        animating = false;
		
				
				
				
				
				
var vInt=0; // this variable controls the loop
var refresh=0; // refresh when a time finish
var interval=1000; // the loop interval
var countque = 0;			
		
				
	(function($) { 


// this function autostarts the infinite loop, every second, triggers the countdown fn
jQuery.autocountdown = function () {
	$('.countdown').countdown(); // trigger the fn
	vInt=setInterval("$('.countdown').countdown();", interval); // set the loop
}

// countdown function, update second-by-second the time to finish
jQuery.fn.countdown = function (options) {
	var defaults = {  // set up default options
		refresh:     0,		 // refresh when a time finish
		interval:    1000, // the loop interval
		cdClass:     'countdown', // the class to apply this plugin
		granularity: 3,
		
		label:    ['w ', 'd ', 'h', ':', ''],
		units:    [604800, 86400, 3600, 60, 1]
	};
	if (options && options.label) {
		$.extend(defaults.label, options.label);
		delete options.label;
	}
    if (options && options.units) {
      $.extend(defaults.units, options.units);
      delete options.units;
    }
	$.extend(defaults, options);

	// pad fn, add left zeros to the string
	var pad = function (value, length) {
		value = value - 2;
		value = String(value);
		length = parseInt(length) || 2;
		while (value.length < length)
			value = "0" + value;
		if (value<1) value = "00";
		return value;
	};

	var format_interval = function (timestamp) {
		var label = defaults.label;
		var units = defaults.units;
		var granularity = defaults.granularity;

		output = '';
		for (i=1; i<=units.length; i++) {
			value=units[i];
			if (timestamp >= value) {	      				
				var val=pad(Math.floor(timestamp / value), 2);
				val = val>0 ? val : '00';
				output += val + label[i];
				timestamp %= value;
				granularity--;
			} 
			else if (value==1) output += '00'; // we need the final seconds to allways show 00, i.e., 03:00

			if (granularity == 0)
				break; 
		}
		
		if (output.length<3) output = '00:'+output;
		return output ? output : '00:00';
	}
	
	// the countdown core
	return this.each(function() {
		secs=$(this).attr('secs');
		$(this).html(format_interval(secs));
		secs--;

			$(this).attr('secs', secs);
			

					if (secs==0){ 
				countque = countque + 1;	
				if (countque == sayim+2){
					$(this).attr('secs', '00');
					document.yolla.submit(); }
				else {
				$(this).attr('secs', sec);
				
		        if (animating == false) {
                animating = true;
                animateLeft = parseInt(jQuery(".container",p).css("left")) - (aNewsSlider.itemWidth * 1);
                if (animateLeft + parseInt(jQuery(".container",p).css("width")) > 0) {
                    jQuery(".prev",p).css("display","block");
                    jQuery(".container",p).animate({left: animateLeft}, s.newsSpeed, function() {
                        jQuery(this).css("left",animateLeft);
                        if (parseInt(jQuery(".container",p).css("left")) + parseInt(jQuery(".container",p).css("width")) <= aNewsSlider.itemWidth * 1) {
                            jQuery(".next",p).css("display","none");
                        }
                        animating = false;
                    });
                } else {
                    animating = false;
                }
            }
            return false;

			if (refresh)
				window.location.href = window.location.href;
		}
			
		
					}});

}
								
	
			$(".next",p).click(function() {	
										
		      if (animating == false) {
                animating = true;
				countque = countque + 1;
					  if (countque == 1) {
					        $.autocountdown();  	 // loop
				    } else {
				$('.countdown').each( function() {$(this).attr('secs', $(this).attr('tsecs'));});
				}
				
                animateLeft = parseInt(jQuery(".container",p).css("left")) - (aNewsSlider.itemWidth * 1);
                if (animateLeft + parseInt(jQuery(".container",p).css("width")) > 0) {
                    jQuery(".prev",p).css("display","block");
                    jQuery(".container",p).animate({left: animateLeft}, s.newsSpeed, function() {
                        jQuery(this).css("left",animateLeft);
                        if (parseInt(jQuery(".container",p).css("left")) + parseInt(jQuery(".container",p).css("width")) <= aNewsSlider.itemWidth * 1) {
                            jQuery(".next",p).css("display","none");
                        }
                        animating = false;
                    });
                } else {
                    animating = false;
                }
            }
            return false;
        });
	
})(jQuery);
	
    },
    vAll: function(s,p) {
        var o = p;
        while (p) {
            p = p.parentNode;
            if (jQuery(p).attr("class") != undefined && jQuery(p).attr("class").indexOf("news_slider") != -1) {
                break;
            }
        }
        if (jQuery(o).text().indexOf("View All") != -1) {
            jQuery(".next",p).css("display","none");
            jQuery(".prev",p).css("display","none");
            jQuery(o).text("View Less");
            jQuery(".container",p).css("left","0px").css("width",aNewsSlider.itemWidth * 1 + "px");
        } else {
            jQuery(o).text("View All");
            aNewsSlider.init(s,p);
        }
    }
};
