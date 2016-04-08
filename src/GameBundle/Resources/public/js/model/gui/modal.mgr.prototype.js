'use strict';

class ModalMgr {
    constructor() {
        this.$html = $('#modal-area');
    }

    /**
     * @returns {ModalMgr}
     */
    show() {
        this.$html.removeClass('hidden').find('.modal').modal({ keyboard: false });

        return this;
    }

    /**
     * @returns {ModalMgr}
     */
    hide() {
        this.$html.find('.modal').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();

        return this;
    }

    /**
     * @param {string} html
     *
     * @returns {ModalMgr}
     */
    updateHTML(html) {
        this.$html.html(html);

        return this;
    }

    /**
     * @param {boolean} [enable]
     *
     * @returns {ModalMgr}
     */
    unlockSubmission(enable) {
        let $button = this.$html.find('button.btn[type="button"]');

        undefined === enable || enable
            ? $button.removeAttr('disabled')
            : $button.attr('disabled', 'disabled');

        return this;
    }
}
