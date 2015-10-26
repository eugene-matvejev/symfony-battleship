function ModalMgr() {
    this.$area = $('#' + ModalMgr.container);
}

ModalMgr.prototype = {
    show: function() {
        this.$area.find('.modal').modal({ keyboard: false });
    },
    hide: function() {
        this.$area.find('.modal').modal('hide');
    },
    updateHTML: function(html) {
        this.$area.html(html);
    },
    enableSubmision: function() {
        this.$area.find('button.btn[type="button"]').removeAttr('disabled');
    },
    disableSubmision: function() {
        this.$area.find('button.btn[type="button"]').attr('disabled', 'disabled');
    }
};

ModalMgr.container = 'modal-area';
