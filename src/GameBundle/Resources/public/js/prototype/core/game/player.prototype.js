function Player($area, name, type, size) {
    var _config = Player.resources.config;

    this.$html = Player.resources.html.layout();
    this.setId('undefined').setName(name).setType(type !== undefined ? _config.type.cpu : _config.type.human)
    this.battlefield = (new Battlefield(size, this.$html.find('.' + _config.trigger.css.field)));

    $area.append(this.$html);

    if(this.type == Player.resources.config.type.human)
        this.battlefield.mockData();
}

Player.prototype = {
    setId: function(id) {
        this.id = id;
        this.htmlUpdate(Player.resources.config.trigger.id, id);

        return this;
    },
    setType: function(type) {
        this.type = type;
        this.htmlUpdate(Player.resources.config.trigger.type, type);

        return this;
    },
    setName: function(name) {
        this.name = name;
        this.htmlUpdate(Player.resources.config.trigger.css.name, name);

        return this;
    },
    getJSON: function() {
        return {id: this.id, name: this.name, type: this.type};
    },
    htmlUpdate: function(type, val) {
        var _trigger = Player.resources.config.trigger;

        switch(type) {
            case _trigger.id:
            case _trigger.type:
                this.$html.attr(type, val);
                break;
            case _trigger.css.name:
                this.$html.find('>.' + type).text(val);
                break;
        }
    }
};

Player.resources = {};
Player.resources.config = {
    trigger: {
        id: 'data-player-id',
        type: 'data-player-type',
        css: {
            name: 'player-name',
            field: 'player-field'
        }
    },
    type: {
        cpu: 1,
        human: 2
    }
};
Player.resources.html = {
    layout: function() {
        var _trigger = Player.resources.config.trigger;

        return $($.parseHTML(
            '<div class="col-md-6 player-area" ' + _trigger.id + '="unk" ' + _trigger.type + '="unk">' +
                '<div class="player-name">unk</div>' +
                '<div class="player-field"></div>' +
            '</div>'
        ));
    }
};
