'use strict';

class PopupMgr {
    constructor() {
        this.$html    = $('#notification-area');
        this.$content = this.$html.find('.notification-content');
    }

    /**
     * @param {string} text
     * @param {string} type
     *
     * @returns {PopupMgr}
     */
    show(text, type) {
        this.$content.html(text);
        this.$html.removeClass().addClass(`alert alert-${type}`);

        return this;
    }

    /**
     * @returns {PopupMgr}
     */
    hide() {
        this.$html.addClass('hidden');

        return this;
    }
}
