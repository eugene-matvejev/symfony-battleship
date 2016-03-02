'use strict';

/**
 *
 * @param {jQuery} $area
 * @param {string} playerName
 * @param {bool}   isCPUPlayer
 * @param {int}    battlefieldSize
 *
 * @constructor
 */
function Player($area, playerName, isCPUPlayer, battlefieldSize) {
    let resources  = Player.resources,
        playerType = resources.config.type;

    this.$html = $(resources.html.layout());
    $area.append(this.$html);

    this.setName(playerName)
        .setType(isCPUPlayer !== undefined || isCPUPlayer ? playerType.cpu : playerType.human);

    this.battlefield = (new Battlefield(battlefieldSize, this.$html.find('.' + resources.config.class.area)));
    if (this.type === playerType.human) {
        this.battlefield.mockData();
    }
}

Player.prototype = {
    /**
     * @type {int}
     */
    id: 'undefined',
    /**
     * @type {string}
     */
    name: 'undefined',
    /**
     * @type {int}
     */
    type: 'undefined',
    /**
     * @param {int} id
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
     * @returns {Object}
     */
    getJSON: function() {
        return {id: this.id, name: this.name, type: this.type};
    },
    /**
     * @param {string}     field
     * @param {string|int} value
     *
     * @returns {void}
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
    attribute: {
        /**
         * @type {string}
         */
        id: 'data-player-id',
        /**
         * @type {string}
         */
        type: 'data-player-type'
    },
    class: {
        /**
         * @type {string}
         */
        name: 'player-name',
        /**
         * @type {string}
         */
        area: 'player-field'
    },
    type: {
        /**
         * @type {int}
         */
        cpu: 1,
        /**
         * @type {int}
         */
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
