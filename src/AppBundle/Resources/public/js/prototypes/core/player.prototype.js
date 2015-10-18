function Player($el, _typeof) {
    this.$html       = $el;
    this.typeof      = _typeof !== undefined ? Player.typeof.cpu : Player.typeof.human;
    this.battlefield = new Battlefield();
    this.battlefield.initData();
    this.battlefield.mockData();
}

Player.prototype = {
    id: 'undefined',
    name: 'undefined',
    typeof: 'undefined',
    setId: function(id) {
        this.id = id;

        return this;
    },
    setName: function(name) {
        this.name = name;

        return this;
    },
    updateHTML: function() {
        var $html = this.getLayoutHTML();
        this.battlefield.setArea($html.find('.player-field'))
                        .updateHTML();
        this.$html.append($html);
    },
    getLayoutHTML: function() {
        return $($.parseHTML('<div class="col-md-6 player-area"' +
                        ' ' + Player.tag.id + '="' + this.id + '"' +
                        ' ' + Player.tag.typeof + '="' + this.typeof + '">' +
                                 '<div class="player-name">' + this.name + '</div>' +
                                 '<div class="player-field"></div>' +
                             '</div>'));
    }
};

Player.tag    = { id: 'data-player-id', typeof: 'data-player-typeof' };
Player.typeof = { cpu: 1, human: 2 };