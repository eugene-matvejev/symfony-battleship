'use strict';

class Player {
    /**
     * @param {string}  playerName
     * @param {boolean} isCPUPlayer
     * @param {number}  battlefieldSize
     */
    constructor(playerName, isCPUPlayer, battlefieldSize) {
        this.$html = $(this.constructor.resources.layout);
        let flags  = this.constructor.resources.flags;

        /** by default: type: human */
        this.setId('undefined')
            .setName(playerName)
            .setFlag(isCPUPlayer ? flags.ai : flags.none);

        this.battlefield = (new Battlefield(this.$html.find('.player-field'), battlefieldSize, this));
        if (this.isHuman()) {
            this.battlefield.initPlayerCells();
        }
    }

    /**
     * @param {number|string} id
     *
     * @returns {Player}
     */
    setId(id) {
        this.id = id;
        this.$html.attr('data-player-id', id);

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
     * @param {number} flag
     *
     * @returns {Player}
     */
    setFlag(flag) {
        this.flag = flag;
        this.$html.attr('data-player-flag', flag);

        return this;
    }

    /**
     * @returns {boolean}
     */
    isCPU() {
        let flag = this.constructor.resources.flags.ai;

        return (this.flags & flag) === flag;
    }

    /**
     * @returns {boolean}
     */
    isHuman() {
        return !this.isCPU();
    }

    /**
     * @returns {{id: {number}, name: {string}, flag: {number}}}
     */
    getJSON() {
        return { id: this.id, name: this.name, flag: this.flag };
    }
}

Player.resources = {
    /** @enum {number} */
    flags: {
        none: 0x0000,
        ai: 0x0001
    },
    /** @type {string} */
    layout: ' \
        <div class="col-md-6 player-area" data-player-id="unk" data-player-type="unk"> \
            <div class="player-name">undefined</div> \
            <div class="player-field"></div> \
        </div>'
};
