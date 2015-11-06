function ModalMgr() {
    this.$area = $('#modal-area');
}

ModalMgr.prototype = {
    show: function() {
        this.$area.find('.modal').modal({ keyboard: false });

        return this;
    },
    hide: function() {
        this.$area.find('.modal').modal('hide');
        $('.modal-backdrop.fade.in').remove(); //TODO REMOVE HACK

        return this;
    },
    updateHTML: function(html) {
        this.$area.html(html);

        return this;
    },
    enableSubmision: function() {
        this.$area.find('button.btn[type="button"]').removeAttr('disabled');

        return this;
    },
    disableSubmision: function() {
        this.$area.find('button.btn[type="button"]').attr('disabled', 'disabled');

        return this;
    }
};