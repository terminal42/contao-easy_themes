/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Yanick Witschi 2010
 * @author     Yanick Witschi <http://www.certo-net.ch>
 * @package    Backend
 * @license    LGPL
 * @filesource
 */
 
 
  /**
 * Class EasyThemes
 *
 * Provide methods to simplify the themes handling.
 * @copyright  Yanick Witschi 2010
 * @author     Yanick Witschi <http://www.certo-net.ch>
 * @package    Backend
 */
var EasyThemes = new Class({
	Implements: [Options],
	options:
	{
		handle: null,
		container: null,
		mode: 'contextmenu'
	},
	initialize: function(options)
	{
		this.setOptions(options);
		
		// abort if the handle is null (layout section collapsed)
		if(!$type(this.options.handle))
		{
			this.options.container.destroy();
			return;
		}
		
        // hide container - IE bug
        this.hideContainer(true);

        switch(this.options.mode)
        {
            case 'contextmenu':
                this.doContextmenu();
                break;
            
            case 'mouseover':
                this.doMouseover();
                break;
            
            case 'inject':
                this.doInject();
                break;
        }
	},
	visible: false,
	onContainer: false,


	/**
	 * Contextmenu
	 */
	doContextmenu: function()
	{
		// Register contextmenu event on handle
		$(this.options.handle).addEvent('contextmenu', function(e)
		{
			e.preventDefault();
			this.showContainer();
		}.bind(this));
		
		// Register click event on document body
		$(document.body).addEvent('click', function()
		{
			if(this.visible)
			{
				this.hideContainer();
			}
		}.bind(this));
	},
	
	
	/**
	 * Mouseover
	 */
	doMouseover: function()
	{
		// Register mouseover event on handle
		$(this.options.handle).addEvent('mouseover', function()
		{
			this.showContainer();
			
			// hide the container automatically after 2 seconds if the cursor is not on the container
			this.checkCursor.delay(2000, this);
		}.bind(this));

		// Register mouseover event on container
		$(this.options.container).addEvent('mouseover', function()
		{
			this.onContainer = true;
		}.bind(this));
	
		// Register mouseleave event on container
		$(this.options.container).addEvent('mouseleave', function()
		{
			this.onContainer = false;
			if(this.visible)
			{
				this.hideContainer();
			}
		}.bind(this));
	},
	
	
	/**
	 * DOM-Inject
	 */
	doInject: function()
	{
		var li = this.options.handle.getParent();
		var content = this.options.container.getChildren();
		content.inject(li, 'after');
	},	
	
	
	/**
	 * Check cursor to decide wheter to hide the container or not
	 */
	checkCursor: function()	
	{
		if(!this.onContainer)
		{
			this.hideContainer();
		}
	},


	/**
	 * Show container
	 */
	showContainer: function()
	{
		this.visible = true;
		// get Position and set to container
		var ContaoContainerMargin = $('container').getCoordinates().top;
		var objCoordinates = $(this.options.handle).getCoordinates();
		$(this.options.container).setStyle('top', objCoordinates.top - ContaoContainerMargin);
		
		$(this.options.container).set('tween', {
			onStart: function(el)
			{
				el.setStyle('z-index', 99);
			}
		}).fade('in');
	},
	
	
	/**
	 * Hide container
	 * @param boolean
	 */
	hideContainer: function(noFade)
	{
	    noFade = noFade || false;
	    var fadeStyle = (noFade) ? 'hide' : 'out';
	    
		this.visible = false;
		
		$(this.options.container).set('tween', {
			onComplete: function(el)
			{
				el.setStyle('z-index', -99);
			}
		}).fade(fadeStyle);
	}
});