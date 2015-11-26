function AlertMgr() {
    this.$area    = $('#notification-area');
    this.$content = this.$area.find('.notification-content');
}

AlertMgr.prototype = {
    show: function(txt, type) {
        this.$content.html(txt);
        this.colorByType(type);
        this.$area.removeClass(PageMgr.resources.config.trigger.css.hidden);

        return this;
    },
    hide: function() {
        this.$area.addClass(PageMgr.resources.config.trigger.css.hidden);

        return this;
    },
    colorByType: function(type) {
        var _type = AlertMgr.resources.config.type;
        switch(type) {
            case _type.info:
            case _type.success:
            case _type.warning:
            case _type.error:
                this.$area.removeClass().addClass('alert alert-' + type);
                break;
        }

        return this;
    }
};

AlertMgr.resources = {
    config: {
        type: {
            info: 'info',
            success: 'success',
            warning: 'warning',
            error: 'danger'
        }
    }
};