function Player() {
}

Player.prototype = {
    data: {},
    id: undefined,
    name: undefined,
    $area: undefined,
    battlefield: undefined,
    init: function() {
        this.initHTML();

        return this;
    },
    setId: function(id) {
        this.id = id;

        return this;
    },
    setName: function(name) {
        this.name = name;

        return this;
    },
    setBattlefield: function(battlefield) {
        this.battlefield = battlefield;

        return this;
    },
    setArea: function($el) {
        this.$area = $el;

        return this;
    },
    initHTML: function() {
        var $html = this.getLayoutHTML();
        this.battlefield.setArea($html.find('.player-field'))
                        .init();
        this.$area.append($html);
    },
    getLayoutHTML: function() {
        return $($.parseHTML(
            '<div class="col-md-6 player-area ">' +
                '<div class="player-name">' +
                    this.name +
                '</div>' +
                '<div class="player-field" data-player-id="' + this.id + '"></div>' +
            '</div>'));
    }
};