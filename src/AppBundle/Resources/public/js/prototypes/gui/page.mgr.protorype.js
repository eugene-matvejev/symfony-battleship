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
        var _css = PageMgr.resources.config.trigger.css;

        this.$content.toggleClass(_css.toggle);
        this.$sidebar.toggleClass(_css.toggle);

        return this;
    },
    switchSection: function(el) {
        var _config = PageMgr.resources.config;

        this.toggleTitle(el);
        this.hideAll();
        this.alertMgr.hide();

        switch(el.getAttribute(_config.trigger.action)) {
            case _config.action.game.new:
                break;
            default:
                var _section = el.getAttribute(_config.trigger.section);
                switch(_section) {
                    case _config.section.game:
                    case _config.section.statistics:
                        this.show(_section);
                        break;
                }
                break;
        }

        return this;
    },
    hideAll: function() {
        var _css = PageMgr.resources.config.trigger.css;

        this.$content.find('.container-fluid>.row>div:not(#notification-area):not(#debug-area)').addClass(_css.hidden);
        this.$sidebar.find('li:not(.sidebar-brand)').removeClass(_css.selected);

        return this;
    },
    show: function(id) {
        var _css = PageMgr.resources.config.trigger.css;

        this.$content.find('div#' + id).removeClass(_css.hidden);
        this.$sidebar.find('li[data-section="' + id + '"]').addClass(_css.selected);

        return this;
    },
    toggleTitle: function(el) {
        var postfix = el.innerText,
            prefix  = this.$sidebar.find('.' + PageMgr.resources.config.trigger.css.title).text();

        this.$docTitle.text(prefix + ' :: ' + postfix);
        this.$pageTitle.text(postfix);

        return this;
    },
    loadingMode: function(enable) {
        var _css = PageMgr.resources.config.trigger.css;

        if(enable) {
            this.$loading.removeClass(_css.hidden);
            this.modalMgr.updateHTML('').show();
        } else {
            this.$loading.addClass(_css.hidden);
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
                selected: 'selected',
                title: 'page-header',
                toggle: 'toggled',
                hidden: 'hidden'
            },
            action: 'data-action',
            section: 'data-section'
        }
    }
};