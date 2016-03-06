'use strict';

/**
 *
 * @param {jQuery}  $area
 * @param {string}  playerName
 * @param {boolean} isCPUPlayer
 * @param {int}     battlefieldSize
 *
 * @constructor
 */
function Player($area, playerName, isCPUPlayer, battlefieldSize) {
    let resources = Player.resources,
        type      = resources.config.type;

    this.$html = $(resources.html.layout());
    $area.append(this.$html);
    //this.id = 'undefined';
    //this.name = 'undefined';
    //this.type = 'undefined';

    this.setId('undefined')
        .setName(playerName)
        .setType(isCPUPlayer ? type.cpu : type.human);

    this.battlefield = (new Battlefield(battlefieldSize, this.$html.find('.' + resources.config.class.area)));
    if (this.isHuman()) {
        this.battlefield.mockData();
    }
}

/**
 * @property {int|string} id
 * @property {int}        type
 * @property {string}     name
 */
Player.prototype = {
    /**
     * @param {string|int} id
     *
     * @returns {Player}
     */
    setId: function(id) {
        this.id = id;
        this.updateHTML(Player.resources.config.attribute.id, id);

        return this;
    },
    /**
     * @param {string} name
     *
     * @returns {Player}
     */
    setName: function(name) {
        this.name = name;
        this.updateHTML(Player.resources.config.class.name, name);

        return this;
    },
    /**
     * @param {int} type
     *
     * @returns {Player}
     */
    setType: function(type) {
        this.type = type;
        this.updateHTML(Player.resources.config.attribute.type, type);

        return this;
    },
    /**
     * @returns {boolean}
     */
    isCPU: function() {
        return this.type === Player.resources.config.type.cpu
    },
    /**
     * @returns {boolean}
     */
    isHuman: function() {
        return !this.isCPU();
    },
    /**
     * @returns {{id: {int}, name: {string}, type: {int}}}
     */
    getJSON: function() {
        return {id: this.id, name: this.name, type: this.type};
    },
    /**
     * @param {string}     field
     * @param {string|int} value
     */
    updateHTML: function(field, value) {
        let config = Player.resources.config;

        switch (field) {
            case config.attribute.id:
            case config.attribute.type:
                this.$html.attr(field, value);
                break;
            case config.class.name:
                this.$html.find('>.' + field).text(value);
                break;
        }
    }
};

Player.resources = {};
Player.resources.config = {
    /**
     * @enum {string}
     */
    attribute: {
        id: 'data-player-id',
        type: 'data-player-type'
    },
    /**
     * @enum {string}
     */
    class: {
        name: 'player-name',
        area: 'player-field'
    },
    /**
     * @enum {int}
     */
    type: {
        cpu: 1,
        human: 2
    }
};
Player.resources.html = {
    /**
     * @returns {string}
     */
    layout: function() {
        let config = Player.resources.config;

        return '' +
            '<div class="col-md-6 player-area" ' + config.attribute.id + '="unk" ' + config.attribute.type + '="unk">' +
                '<div class="' + config.class.name + '">unk</div>' +
                '<div class="' + config.class.area +'"></div>' +
            '</div>';
    }
};
