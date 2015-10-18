function PageMgr() {
    this.$docTitle  = $('head>title');
    this.$container = $('div.container');
    this.$loading   = this.$container.find('div.page-loading');
    this.$sidebar   = this.$container.find('div.page-sidebar');
    this.$content   = this.$container.find('div.page-content');
    this.$pageTitle = this.$content.find('.page-section-title');
}

PageMgr.prototype = {
    toggleSidebar: function() {
        this.$content.toggleClass(PageMgr.classes.toggle);
        this.$sidebar.toggleClass(PageMgr.classes.toggle);
    },
    switchSection: function(el) {
        console.log(el);
        this.toggleTitle(el);
        switch(el.getAttribute(PageMgr.index.action)) {
            case PageMgr.action.game._new:
                break;
            default:
                switch(el.getAttribute(PageMgr.index.section)) {
                    case PageMgr.section.game:
                        //this.toggleTitle($(this));
                        break;
                    case PageMgr.section.stats:
                        break;
                }
                break;
        }
    },
    toggleTitle: function(el) {
        var postfix = el.innerText,//.text(),
            prefix  = this.$sidebar.find('.' + PageMgr.classes.title).text();
        //var postfix = $el.text(),
        //    prefix  = this.$sidebar.find('.' + PageMgr.classes.title).text();
        this.$docTitle.text(prefix + ' :: ' + postfix);
        this.$pageTitle.text(postfix);

        return this;
    },
    loadingMode: function(enable) {
        if(enable) {
            this.$loading.removeClass(PageMgr.classes.hidden);
            this.$container.addClass(PageMgr.classes.locked);
        } else {
            this.$loading.addClass(PageMgr.classes.hidden);
            this.$container.removeClass(PageMgr.classes.locked);
        }

        return this;
    }
};

PageMgr.index   = { action: 'data-action', section: 'data-section' };
PageMgr.classes = { toggle: 'toggled', title: 'page-header', hidden: 'hidden', locked: 'no-scroll-mode' };
PageMgr.section = { game: 'game-area', stats: 'stats-area' };
PageMgr.action  = { game: { _new: 'game-new' } };
