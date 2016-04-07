'use strict';

class Player {
    /**
     * @param {string}  playerName
     * @param {boolean} isCPUPlayer
     * @param {int}     battlefieldSize
     */
    constructor(playerName, isCPUPlayer, battlefieldSize) {
        this.$html = $(Player.resources.layout);
        let type   = Player.resources.type;

        this.setId('undefined')
            .setName(playerName)
            .setType(isCPUPlayer ? type.cpu : type.human);

        this.battlefield = (new Battlefield(this.$html.find('.player-field'), battlefieldSize, this));
        if (this.isHuman()) {
            this.battlefield.initPlayerCells();
        }
    }

    /**
     * @param {int|string} id
     *
     * @returns {Player}
     */
    setId(id) {
        this.id = id;
        this.$html.attr('data-player-id', id)

        return this;
    }

    /**
     * @param {string} name
     *
     * @returns {Player}
     */
    setName(name) {
        this.name = name;
        this.$html.find('>.player-name').text(name);

        return this;
    }

    /**
     * @param {int} type
     *
     * @returns {Player}
     */
    setType(type) {
        this.type = type;
        this.$html.attr('data-player-type', type);

        return this;
    }

    /**
     * @returns {boolean}
     */
    isCPU() {
        return this.type === Player.resources.type.cpu
    }

    /**
     * @returns {boolean}
     */
    isHuman() {
        return !this.isCPU();
    }

    /**
     * @returns {{id: {int}, name: {string}, type: {int}}}
     */
    getJSON() {
        return { id: this.id, name: this.name, type: this.type };
    }
}

Player.resources = {
    /** @enum {string} */
    attribute: {
        id: 'data-player-id',
        type: 'data-player-type'
    },
    /** @enum {string} */
    class: {
        name: 'player-name',
        area: 'player-field'
    },
    /** @enum {int} */
    type: {
        cpu: 1,
        human: 2
    },
    /**
     * @returns {string}
     */
    layout: '\
        <div class="col-md-6 player-area" data-player-id="unk" data-player-type="unk"> \
            <div class="player-name">undefined</div> \
            <div class="player-field"></div> \
        </div>'
};
