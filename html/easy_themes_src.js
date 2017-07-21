var EasyThemes = new Class({

    Implements: [ Options ],

    options: {
        mode: 'contextmenu',
        delay: 500,
        isContao4: false
    },

    container: null,
    layoutSection: null,
    themeHandle: null,
    intTimeoutId: 0,
    shouldRun: true,
    isCollapsed: null,
    layoutSectionLoaded: false,

    initialize: function (options) {
        var self = this;
        this.setOptions(options);

        this.layoutSection = document.getElementById('tl_navigation').getElements('.easy_themes_toggle')[0];
        this.container = document.getElementById('easy_themes');

        // get state
        this.isCollapsed = !!(this.layoutSection.hasClass('easy_themes_collapsed'));

        // check if the layout section content is loaded or if it is going to be loaded via ajax on the next click
        this.layoutSectionLoaded = Boolean($$('#tl_navigation a.themes').length);

        // initialize easy_themes again when someone toggles the layout section
        this.layoutSection.addEvent('click', function () {
            // update state
            self.isCollapsed = !self.isCollapsed;
            self.init();
        });

        window.addEvent('ajax_change', function () {
            self.layoutSectionLoaded = true;
            self.init();
        });

        this.init();
    },

    init: function () {
        var self = this;

        // only launch easy_themes if expanded and the data doesn't need to be loaded via ajax first
        if (!this.isCollapsed && this.layoutSectionLoaded) {
            this.themeHandle = $$('#tl_navigation a.themes')[0].getParent('li');
            this.container.inject(this.themeHandle);
            this.container.removeClass('easy_themes_doNotLaunch');
        }
        else {
            this.container.addClass('easy_themes_doNotLaunch');
            return;
        }

        switch (this.options.mode) {
            case 'contextmenu':
                this.themeHandle.addEvent('contextmenu', function (e) {
                    e.preventDefault();
                    self.container.fade("in");
                });
                $(document.body).addEvent('click', this.container.fade.pass("out", this.container));
                break;

            case 'mouseover':
                this.themeHandle.addEvent('mouseenter',function (e) {
                    clearTimeout(self.intTimeoutId);
                    self.container.fade("in");
                }).addEvent('mouseleave', function (e) {
                    self.intTimeoutId = self.container.fade.delay(self.options.delay, self.container, "out");
                });
        }

        // Set item to display: block when everything is done
        this.container.addClass('ready');
    }
});
