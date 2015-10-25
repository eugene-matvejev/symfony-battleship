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
    isModalFilled: function() {
        var self = this;
        this.$area.find('.form-group>input').each(function() {
            if(this.value.length < 1 || this.id == Game.indexes.modal.battlefieldSize && this.value < Game.limits.minBattlefieldSize) {
                self.$area.find('button.btn[type="button"]').attr('disabled', 'disabled');
                return false;
            }
        });

        this.$area.find('button.btn[type="button"]').attr('disabled', false);

        return true;
    }
};

ModalMgr.container = 'modal-area';
