function Player($el, name, type) {
    this.$html = $el;
    this.name  = name;
    this.type  = type !== undefined ? Player.resource.config.type.cpu : Player.resource.config.type.human;
}

Player.prototype = {
    id: 'undefined',
    initBattlefield: function(size) {
        this.battlefield = (new Battlefield(size)).initData();
        if(this.type == Player.resource.config.type.human)
            this.battlefield.mockData();

        return this;
    },
    setId: function(id) {
        this.id = id;

        return this;
    },
    getJSON: function() {
        return {id: this.id, name: this.name, type: this.type};
    },
    updateHTML: function() {
        var $html = this.getLayoutHTML();
        this.battlefield.setArea($html.find('.player-field'))
                        .updateHTML();
        this.$html.append($html);
    },
    getLayoutHTML: function() {
        return $($.parseHTML('<div class="col-md-6 player-area"' +
                        ' ' + Player.resource.config.trigger.id + '="' + this.id + '"' +
                        ' ' + Player.resource.config.trigger.type + '="' + this.type + '">' +
                                 '<div class="player-name">' + this.name + '</div>' +
                                 '<div class="player-field"></div>' +
                             '</div>'));
    }
};

Player.resource = {
    config: {
        trigger: {
            id: 'data-player-id',
            type: 'data-player-type'
        },
        type: {
            cpu: 1,
            human: 2
        }
    }
};
