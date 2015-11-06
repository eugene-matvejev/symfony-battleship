function PageMgr() {
    this.modalMgr   = new ModalMgr();
    this.alertMgr   = new AlertMgr();
    this.$docTitle  = $('head>title');
    this.$document  = $('.container');
    this.$loading   = this.$document.find('.page-loading');
    this.$sidebar   = this.$document.find('.page-sidebar');
    this.$content   = this.$document.find('.page-content');
    this.$pageTitle = this.$content.find('.page-section-title');
}
PageMgr.prototype = {
    toggleSidebar: function() {
        this.$content.toggleClass(PageMgr.resource.config.trigger.css.toggle);
        this.$sidebar.toggleClass(PageMgr.resource.config.trigger.css.toggle);
    },
    switchSection: function(el) {
        this.toggleTitle(el);
        this.hideAll();
        this.alertMgr.hide();
        var config = PageMgr.resource.config;
        switch(el.getAttribute(config.trigger.action)) {
            case config.action.game.new:
                break;
            default:
                var section = el.getAttribute(config.trigger.section);
                switch(section) {
                    case config.section.game:
                    case config.section.statistics:
                        this.show(section);
                        break;
                }
                break;
        }
    },
    hideAll: function() {
        $('div#game-area, div#stats-area').addClass(PageMgr.resource.config.trigger.css.hidden);
    },
    show: function(id) {
        $('div#' + id).removeClass(PageMgr.resource.config.trigger.css.hidden);
    },
    toggleTitle: function(el) {
        var postfix = el.innerText,
            prefix  = this.$sidebar.find('.' + PageMgr.resource.config.trigger.css.title).text();
        this.$docTitle.text(prefix + ' :: ' + postfix);
        this.$pageTitle.text(postfix);

        return this;
    },
    loadingMode: function(enable) {
        if(enable) {
            this.$loading.removeClass(PageMgr.resource.config.trigger.css.hidden);
            this.modalMgr.updateHTML('').show();
        } else {
            this.$loading.addClass(PageMgr.resource.config.trigger.css.hidden);
            this.modalMgr.updateHTML('').hide();
        }

        return this;
    }
};

PageMgr.resource = {
    config: {
        action: {
            game: {
                new: 'game-new'
            }
        },
        section: {
            game: 'game-area',
            statistics: 'stats-area'
        },
        trigger: {
            css: {
                toggle: 'toggled',
                title: 'page-header',
                hidden: 'hidden'
            },
            action: 'data-action',
            section: 'data-section'
        }
    }
};