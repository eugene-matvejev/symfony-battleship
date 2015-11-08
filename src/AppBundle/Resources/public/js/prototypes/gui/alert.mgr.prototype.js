function AlertMgr() {
    this.$area    = $('#notification-area');
    this.$content = this.$area.find('.notification-content');
}

AlertMgr.prototype = {
    show: function(txt, type) {
        this.$content.html(txt);
        this.$area.removeClass(PageMgr.resources.config.trigger.css.hidden);
        this.applyColor(type);

        return this;
    },
    hide: function() {
        this.$area.addClass(PageMgr.resources.config.trigger.css.hidden);

        return this;
    },
    applyColor: function(type) {
        console.log(type);
        switch(type) {
            case AlertMgr.resources.config.type.info:
            case AlertMgr.resources.config.type.success:
            case AlertMgr.resources.config.type.warning:
            case AlertMgr.resources.config.type.error:
                console.log(type);

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