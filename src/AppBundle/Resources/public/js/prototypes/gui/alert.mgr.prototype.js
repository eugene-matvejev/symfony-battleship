function AlertMgr() {
    this.$container = $('#notification-area');
    this.$content   = this.$container.find('.' + AlertMgr.classes.content);
}

AlertMgr.prototype = {
    show: function(txt, type) {
        this.$content.html(txt);
        this.applyColor(type);
        this.$container.removeClass('hidden');

        return this;
    },
    hide: function() {
        this.$container.addClass('hidden');

        return this;
    },
    applyColor: function(type) {
        switch(type) {
            case AlertMgr.type.success:
            case AlertMgr.type.info:
            case AlertMgr.type.warning:
            case AlertMgr.type.error:
                this.$container.removeClass().addClass('alert alert-' + type);
                break;
        }

        return this;
    }
};

AlertMgr.classes = { content: 'notification-content' };
AlertMgr.type    = { success: 'success', info: 'info', warning: 'warning', error: 'danger' };