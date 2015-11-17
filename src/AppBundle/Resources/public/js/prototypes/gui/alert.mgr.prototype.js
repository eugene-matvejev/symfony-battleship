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
        var _config = AlertMgr.resources.config;
        switch(type) {
            case _config.type.info:
            case _config.type.success:
            case _config.type.warning:
            case _config.type.error:
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