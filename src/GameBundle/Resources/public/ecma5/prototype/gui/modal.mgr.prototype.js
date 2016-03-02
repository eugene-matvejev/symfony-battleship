function ModalMgr() {
    this.$area = $('#modal-area');
}

ModalMgr.prototype = {
    show: function() {
        this.$area.removeClass(PageMgr.resources.config.trigger.css.hidden);
        this.$area.find('.modal').modal({ keyboard: false });

        return this;
    },
    hide: function() {
        this.$area.find('.modal').modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop.fade.in').remove();

        return this;
    },
    updateHTML: function(html) {
        this.$area.html(html);

        return this;
    },
    unlockSubmission: function(enable) {
        var $btn = this.$area.find('button.btn[type="button"]');
        enable === undefined || enable
            ? $btn.removeAttr('disabled')
            : $btn.attr('disabled', 'disabled');

        return this;
    }
};
