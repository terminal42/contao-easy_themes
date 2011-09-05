/**
 * Class EasyThemes
 *
 * Provide methods to simplify the themes handling.
 * @copyright  Yanick Witschi 2011
 * @author     Yanick Witschi <yanick.witschi@certo-net.ch>
 * @author	   Oliver Hoff <oliver@hofff.com>
 * @package    easy_themes
 */
var EasyThemes = new Class({
	
	Implements: [ Options ],
	
	options: {
		handle: null,
		container: null,
		mode: 'contextmenu',
		delay: 500
	},
	
	intTimeoutId: 0,
	
	initialize: function(options) {
		var self = this;
		this.setOptions(options);
		this.options.container.inject(this.options.handle);

        switch(this.options.mode)
        {
            case 'contextmenu':
            	this.options.handle.addEvent('contextmenu', function(e)
            	{
            		e.preventDefault();
            		self.options.container.fade("in");
            	});
            	$(document.body).addEvent('click', this.options.container.fade.pass("out", this.options.container));
                break;
            
            case 'mouseover':
        		this.options.handle.addEvent('mouseenter', function(e)
        		{
        			clearTimeout(self.intTimeoutId);
        			self.options.container.fade("in");
        		}).addEvent('mouseleave', function(e)
        		{
        			self.intTimeoutId = self.options.container.fade.delay(self.options.delay, self.options.container, "out");
        		});
        }
	}
	
});