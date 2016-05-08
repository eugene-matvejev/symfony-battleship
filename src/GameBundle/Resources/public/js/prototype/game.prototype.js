'use strict';

class Game extends APIRequestService {
    /**
     * @param {jQuery} $el
     */
    constructor($el) {
        super();
        this.$html = $el;

        this.popupMgr = new PopupMgr();
        this.modalMgr = new ModalMgr();
    }

    /**
     * @param {{name: {string}, isCPU: {boolean}}[]} players
     * @param {number}                               battlefieldSize
     */
    init(players, battlefieldSize) {
        this.players = [];
        this.$html.html('');

        let self      = this,
            onSuccess = function (response) {
                self.parseInitResponse(response);
            };

        /** construct players */
        players.forEach(player => this.players.push(new Player(player.name, player.isCPU || false, battlefieldSize)), this);
        /** append player's HTML to the document */
        this.players.forEach(player => this.$html.append(player.$html), this);

        this.request('POST', this.$html.attr('data-init-link'), this.players.map(player => player.getSerializationView()), onSuccess);
    }

    /**
     * @param {{
     *     id: {number},
     *     battlefields: {
     *         id: {number},
     *         player: {id: {number}, name: {string}},
     *         cells: {id: {number}, coordinate: {string}, flags: {number}}[]
     *     }[]
     * }} response
     */
    parseInitResponse(response) {
        response.battlefields.forEach(function (battlefield) {
            let player = this.findPlayerByName(battlefield.player.name).setId(battlefield.player.id);

            Object.keys(battlefield.cells).forEach(function (index) {
                let _cell = battlefield.cells[index],
                    cell  = this.findPlayerCellByCriteria({ playerId: player.id, coordinate: _cell.coordinate });

                if (undefined !== cell) {
                    cell.setId(_cell.id).setFlags(_cell.flags);
                }
            }, this);
        }, this);
    }

    /**
     * @param {number} cellId
     */
    update(cellId) {
        this.cellSend(this.findPlayerCellByCriteria({ id: cellId }));
    }

    /**
     * @param id {number}
     *
     * @returns {!Player}
     */
    findPlayerById(id) {
        let player = this.players.find(player => player.id === id);
        if (undefined !== player) {
            return player;
        }

        throw `player with id: ${id} not found`;
    }

    /**
     * @param {string} name
     *
     * @returns {!Player}
     */
    findPlayerByName(name) {
        let player = this.players.find(player => player.name === name);
        if (undefined !== player) {
            return player;
        }

        throw `player with name: "${name}" not found`;
    }

    /**
     * @param {Cell} cell
     */
    cellSend(cell) {
        var self      = this,
            onSuccess = function (response) {
                self.parseUpdateResponse(response);
            };

        this.request('PATCH', this.$html.attr('data-turn-link') + cell.id, undefined, onSuccess);
    }

    /**
     * @param {{cells: {id: {number}, flags: {number}}[], result: {player: {Object}}}} response
     */
    parseUpdateResponse(response) {
        response.cells.forEach(cell => this.findPlayerCellByCriteria({ id: parseInt(cell.id) }).setFlags(cell.flags), this);

        /** detect victory */
        if (undefined !== response.result) {
            let text = this.constructor.resources.config.text;

            this.findPlayerById(response.result.player.id).isAIControlled()
                ? this.popupMgr.show(text.loss, 'error')
                : this.popupMgr.show(text.win, 'success');
        }
    }

    /**
     * @param {{playerId: {number}, id: {number}, coordinate: {string}}} criteria
     *
     * @returns {?Cell}
     */
    findPlayerCellByCriteria(criteria) {
        for (let player of this.players) {
            if (undefined !== criteria.playerId && criteria.playerId !== player.id) {
                continue;
            }

            let cell = player.battlefield.findCellByCriteria(criteria);
            if (undefined !== cell) {
                return cell;
            }
        }

        throw `cell not found by criteria: ${JSON.stringify(criteria)}`;
    }

}

Game.resources          = {};
Game.resources.config   = {
    /** @enum {string} */
    text: {
        win: 'you won',
        loss: 'you lost'
    },
    pattern: {
        /** @enum {number} */
        battlefield: {
            min: 7,
            max: 12
        },
        /** @type {Object} */
        username: /^[a-zA-Z0-9\.\- @]{1,100}$/
    }
};
Game.resources.validate = {
    battlefield: {
        /**
         * @type {number}
         *
         * @returns {boolean}
         */
        size: function (value) {
            let battlefield = Game.resources.config.pattern.battlefield;

            return !isNaN(value) && value >= battlefield.min && value <= battlefield.max;
        }
    },
    /**
     * @type {string}
     *
     * @returns {boolean}
     */
    username: function (value) {
        return Game.resources.config.pattern.username.test(value);
    }
};
Game.resources.html     = {
    /**
     * @returns {string}
     */
    modal: function () {
        let pattern = Game.resources.config.pattern;

        return ` \
            <div class="modal fade"> \
                <div class="modal-dialog"> \
                    <div class="modal-content"> \
                        <div class="modal-header"> \
                            <button type="button" class="close" data-dismiss="modal"> \
                                <span aria-hidden="true">&times;</span> \
                            </button> \
                            <h4 class="modal-title">your details</h4> \
                        </div> \
                        <div class="modal-body"> \
                            <div class="form-group"> \
                                <label class="control-label" for="model-input-player-name">player name</label> \
                                <input type="text" class="form-control" id="model-input-player-name" placeholder=""> \
                                <span class="help-block">pattern: "${pattern.username.toString()}"</span>
                                <span class="help-block">example: eugene.matvejev@gmail.com</span>
                            </div> \
                            <div class="form-group"> \
                                <label class="control-label" for="model-input-battlefield-size">battlefield size</label> \
                                <input type="test" class="form-control" id="model-input-battlefield-size"
                                        placeholder="battlefield size ${pattern.battlefield.min} and ${pattern.battlefield.max}"/> \
                                <span class="help-block">example: 7</span>
                            </div> \
                        </div> \
                        <div class="modal-footer"> \
                            <button type="button" id="model-button-init-new-game" class="btn btn-primary" disabled="disabled">next step</button> \
                        </div> \
                    </div> \
                </div> \
            </div>`;
    }
};
