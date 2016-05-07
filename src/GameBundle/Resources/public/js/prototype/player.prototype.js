'use strict';

class Player {
    /**
     * @param {string}  playerName
     * @param {boolean} isCPUPlayer
     * @param {number}  battlefieldSize
     */
    constructor(playerName, isCPUPlayer, battlefieldSize) {
        this.$html = $(this.constructor.resources.layout);
        /** by default: type: human (0x00) */
        this.setName(playerName)
            .setFlag(isCPUPlayer ? this.constructor.resources.flags.ai : 0x00);

        this.battlefield = (new Battlefield(this.$html.find('.player-field'), battlefieldSize, this));
        if (!this.isAIControlled()) {
            this.battlefield.initPlayerCells();
        }
    }

    /**
     * @param {number} id
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
        this.flags = flag;
        this.$html.attr('data-player-flag', flag);

        return this;
    }

    /**
     * @returns {boolean}
     */
    isAIControlled() {
        let flag = this.constructor.resources.flags.ai;

        return (this.flags & flag) === flag;
    }

    /**
     * @returns {{
     *      id: {number},
     *      flags: {number},
     *      name: {string},
     *      battlefield: {number}
     *      cells: {id: {number}, coordinate: {string}, flags: {number}}[]
     * }}
     */
    getSerializationView() {
        return {
            id: this.id,
            flags: this.flags,
            name: this.name,
            battlefield: this.battlefield.id,
            cells: this.battlefield.cells.map(cell => cell.getSerializationView())
        };
    }
}

Player.resources = {
    /** @enum {number} */
    flags: {
        ai: 0x01
    },
    /** @type {string} */
    layout: ' \
        <div class="col-md-6 player-area" data-player-id="unk" data-player-type="unk"> \
            <div class="player-name">undefined</div> \
            <div class="player-field"></div> \
        </div>'
};
