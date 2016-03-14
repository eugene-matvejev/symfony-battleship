'use strict';

/**
 * @constructor
 */
function AlertMgr() {
    this.$html = $('#notification-area');
    this.$content = this.$html.find('.notification-content');
}

AlertMgr.prototype = {
    /**
     * @param {string} txt
     * @param {string} type
     *
     * @returns {AlertMgr}
     */
    show: function(txt, type) {
        this.colorByType(type);
        this.$content.html(txt);
        this.$html.removeClass(PageMgr.resources.config.trigger.css.hidden);

        return this;
    },
    /**
     * @returns {AlertMgr}
     */
    hide: function() {
        this.$html.addClass(PageMgr.resources.config.trigger.css.hidden);

        return this;
    },
    /**
     * @param {string} _type
     *
     * @returns {AlertMgr}
     */
    colorByType: function(_type) {
        let type = AlertMgr.resources.config.type;

        switch(_type) {
            case type.info:
            case type.success:
            case type.warning:
            case type.error:
                this.$html.removeClass().addClass('alert alert-' + _type);
                break;
        }

        return this;
    }
};

AlertMgr.resources = {
    config: {
        type: {
            /**
             * @type {string}
             */
            info: 'info',
            /**
             * @type {string}
             */
            success: 'success',
            /**
             * @type {string}
             */
            warning: 'warning',
            /**
             * @type {string}
             */
            error: 'danger'
        }
    }
};
