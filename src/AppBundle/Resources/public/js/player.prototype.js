function Player($el) {
    this.$area       = $el;
    this.battlefield = new Battlefield();
    this.battlefield.initData();
    this.battlefield.mockData();
}

Player.prototype = {
    id: 'undefined',
    name: undefined,
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
        this.$area.append($html);
    },
    getLayoutHTML: function() {
        return $($.parseHTML('<div class="col-md-6 player-area ">' +
                                 '<div class="player-name">' + this.name + '</div>' +
                                 '<div class="player-field" data-player-id="' + this.id + '"></div>' +
                             '</div>'));
    }
};