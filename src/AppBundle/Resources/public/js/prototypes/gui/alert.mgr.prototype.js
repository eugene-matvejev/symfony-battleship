function AlertMgr() {
    this.$area    = $('#notification-area');
    this.$content = this.$area.find('.notification-content');
}

AlertMgr.prototype = {
    show: function(txt, type) {
        this.$content.html(txt);
        this.$area.removeClass(PageMgr.resource.config.trigger.css.hidden);
        this.applyColor(type);

        return this;
    },
    hide: function() {
        this.$area.addClass(PageMgr.resource.config.trigger.css.hidden);

        return this;
    },
    applyColor: function(type) {
        console.log(type);
        switch(type) {
            case AlertMgr.resource.config.type.info:
            case AlertMgr.resource.config.type.success:
            case AlertMgr.resource.config.type.warning:
            case AlertMgr.resource.config.type.error:
                console.log(type);

                this.$area.removeClass().addClass('alert alert-' + type);
                break;
        }

        return this;
    }
};

AlertMgr.resource = {
    config: {
        type: {
            info: 'info',
            success: 'success',
            warning: 'warning',
            error: 'danger'
        }
    }
};