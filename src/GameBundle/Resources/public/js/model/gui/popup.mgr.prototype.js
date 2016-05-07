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
     * @returns {!PopupMgr}
     */
    show(text, type) {
        this.colorByType(type);
        this.$content.html(text);

        this.$html.removeClass('hidden');

        return this;
    }

    /**
     * @returns {!PopupMgr}
     */
    hide() {
        this.$html.addClass('hidden');

        return this;
    }

    /**
     * @param {string} type
     *
     * @returns {!PopupMgr}
     */
    colorByType(type) {
        switch (type) {
            case 'success':
            case 'info':
            case 'warning':
            case 'danger':
                this.$html.removeClass().addClass(`alert alert-${type}`);
                break;
        }

        return this;
    }
}
