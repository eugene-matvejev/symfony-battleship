function PageMgr() {
    this.modalMgr   = new ModalMgr();
    this.$docTitle  = $('head>title');
    this.$container = $('.container');
    this.$loading   = this.$container.find('.page-loading');
    this.$sidebar   = this.$container.find('.page-sidebar');
    this.$content   = this.$container.find('.page-content');
    this.$pageTitle = this.$content.find('.page-section-title');
}
//<div id="game-area" data-turn-link="{{ path('battleship.game.api.turn') }}"
//data-start-link="{{ path('battleship.game.api.start') }}">
//</div>
//<div id="stats-area" data-stats-link="{{ path('battleship.game.api.statistics') }}"></div>
//<div id="debug-area"></div>

PageMgr.prototype = {
    toggleSidebar: function() {
        this.$content.toggleClass(PageMgr.classes.toggle);
        this.$sidebar.toggleClass(PageMgr.classes.toggle);
    },
    switchSection: function(el) {
        this.toggleTitle(el);
        this.hideAll();
        switch(el.getAttribute(PageMgr.indexes.action)) {
            case PageMgr.actions.game._new:
                break;
            default:
                var section = el.getAttribute(PageMgr.indexes.section);
                switch(section) {
                    case PageMgr.sections.game:
                    case PageMgr.sections.stats:
                        this.show(section);
                        break;
                }
                break;
        }
    },
    hideAll: function() {
        $('div#game-area, div#stats-area').addClass(PageMgr.classes.hidden);
    },
    show: function(id) {
        $('div#' + id).removeClass(PageMgr.classes.hidden);
    },
    toggleTitle: function(el) {
        var postfix = el.innerText,
            prefix  = this.$sidebar.find('.' + PageMgr.classes.title).text();
        this.$docTitle.text(prefix + ' :: ' + postfix);
        this.$pageTitle.text(postfix);

        return this;
    },
    loadingMode: function(enable) {
        if(enable) {
            this.modalMgr.updateHTML('').show();
            this.$loading.removeClass(PageMgr.classes.hidden);
            this.$container.addClass(PageMgr.classes.locked);
        } else {
            this.modalMgr.updateHTML('').hide();
            this.$loading.addClass(PageMgr.classes.hidden);
            this.$container.removeClass(PageMgr.classes.locked);
        }

        return this;
    }
};

PageMgr.indexes  = { action: 'data-action', section: 'data-section' };
PageMgr.classes  = { toggle: 'toggled', title: 'page-header', hidden: 'hidden', locked: 'no-scroll-mode' };
PageMgr.sections = { game: 'game-area', stats: 'stats-area' };
PageMgr.actions  = { game: { _new: 'game-new' } };