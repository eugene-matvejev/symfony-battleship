'use strict';

class PageMgr {
    constructor() {
        this.modalMgr   = new ModalMgr();
        this.popupMgr   = new PopupMgr();
        this.$docTitle  = $('head>title');
        this.$loading   = $('.page-loading');
        this.$sidebar   = $('.page-sidebar');
        this.$content   = $('.page-content');
        this.$pageTitle = this.$content.find('.page-section-title');
    }

    /**
     * @returns {!PageMgr}
     */
    toggleSidebar() {
        this.$sidebar.toggleClass('toggled');
        this.$content.toggleClass('toggled');

        return this;
    }

    /**
     * @param {!Element} el
     *
     * @returns {!PageMgr}
     */
    switchSection(el) {
        let section = el.getAttribute('data-section');
        switch (section) {
            case 'game-current-area':
            case 'game-results-area':
                this.popupMgr.hide();
                this.toggleTitle(el.innerText);
                this.hideAll();

                this.show(section);
        }

        return this;
    }

    /**
     * @returns {!PageMgr}
     */
    hideAll() {
        this.$content.find('.container-fluid > .row > div:not(#notification-area)').addClass('hidden');
        this.$sidebar.find('li:not(.sidebar-brand)').removeClass('selected');

        return this;
    }

    /**
     * @param {string} id
     *
     * @returns {!PageMgr}
     */
    show(id) {
        this.$content.find(`div#${id}`).removeClass('hidden');
        this.$sidebar.find(`li[data-section="${id}"]`).addClass('selected');

        return this;
    }

    /**
     * @param {string} text
     *
     * @returns {!PageMgr}
     */
    toggleTitle(text) {
        let prefix = this.$sidebar.find('.page-header').text();

        this.$docTitle.text(`${prefix} :: ${text}`);
        this.$pageTitle.text(text);

        return this;
    }

    /**
     * @param {?boolean} [enable]
     *
     * @returns {!PageMgr}
     */
    loadingMode(enable) {
        this.modalMgr.updateHTML('').hide();

        this.$loading.addClass('hidden');

        if (undefined === enable || enable) {
            this.$loading.removeClass('hidden');
            this.modalMgr.show();
        }

        return this;
    }
}
