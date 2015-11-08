function PageMgr() {
    this.modalMgr   = new ModalMgr();
    this.alertMgr   = new AlertMgr();
    this.$docTitle  = $('head>title');
    this.$loading   = $('.page-loading');
    this.$sidebar   = $('.page-sidebar');
    this.$content   = $('.page-content');
    this.$pageTitle = this.$content.find('.page-section-title');
}
PageMgr.prototype = {
    toggleSidebar: function() {
        var toggleClass = PageMgr.resources.config.trigger.css.toggle;

        this.$content.toggleClass(toggleClass);
        this.$sidebar.toggleClass(toggleClass);
    },
    switchSection: function(el) {
        var config = PageMgr.resources.config;

        this.toggleTitle(el);
        this.hideAll();
        this.alertMgr.hide();

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
        this.$content.find('.container-fluid>.row>div:not(#notification-area):not(#debug-area)').addClass(PageMgr.resources.config.trigger.css.hidden);
    },
    show: function(id) {
        $('div#' + id).removeClass(PageMgr.resources.config.trigger.css.hidden);
    },
    toggleTitle: function(el) {
        var postfix = el.innerText,
            prefix  = this.$sidebar.find('.' + PageMgr.resources.config.trigger.css.title).text();
        this.$docTitle.text(prefix + ' :: ' + postfix);
        this.$pageTitle.text(postfix);

        return this;
    },
    loadingMode: function(enable) {
        var hiddenClass = PageMgr.resources.config.trigger.css.hidden;
        if(enable) {
            this.$loading.removeClass(hiddenClass);
            this.modalMgr.updateHTML('').show();
        } else {
            this.$loading.addClass(hiddenClass);
            this.modalMgr.updateHTML('').hide();
        }

        return this;
    }
};

PageMgr.resources = {
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