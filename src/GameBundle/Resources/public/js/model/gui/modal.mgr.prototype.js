'use strict';

/**
 * @constructor
 */
function ModalMgr() {
    this.$html = $('#modal-area');
}

/**
 * @property {jQuery} $html
 */
ModalMgr.prototype = {
    /**
     * @returns {ModalMgr}
     */
    show: function() {
        this.$html.removeClass(PageMgr.resources.config.trigger.css.hidden)
                  .find('.modal').modal({ keyboard: false });

        return this;
    },
    /**
     * @returns {ModalMgr}
     */
    hide: function() {
        this.$html.find('.modal').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop.fade.in').remove();

        return this;
    },
    /**
     * @param {string} html
     *
     * @returns {ModalMgr}
     */
    updateHTML: function(html) {
        this.$html.html(html);

        return this;
    },
    /**
     * @param {boolean} enable
     *
     * @returns {ModalMgr}
     */
    unlockSubmission: function(enable) {
        let $button = this.$html.find('button.btn[type="button"]');

        undefined === enable || enable
            ? $button.removeAttr('disabled')
            : $button.attr('disabled', 'disabled');

        return this;
    }
};
