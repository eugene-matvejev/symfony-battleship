'use strict';

class Game extends APIRequestMgr {
    /**
     * @param {jQuery} $el
     */
    constructor($el) {
        super();
        this.$html = $el;

        this.alertMgr = new AlertMgr();
        this.modalMgr = new ModalMgr();
    }

    /**
     * @param {number|string} id
     *
     * @returns {Game}
     */
    setId(id) {
        this.id = id;

        return this;
    }

    /**
     * @returns {{id: {number}}}
     */
    getJSON() {
        return { id: this.id };
    }

    /**
     * @param {{name: {string}, isCPU: {boolean}}[]} players
     * @param {number}                               battlefieldSize
     */
    init(players, battlefieldSize) {
        this.pageMgr.switchSection(document.querySelector('.page-sidebar li[data-section="game-current-area"]'));

        this.setId('undefined');
        this.players = [];
        this.$html.html('');

        let self        = this,
            requestData = {
                game: this.getJSON(),
                data: []
            },
            onSuccess   = function (response) {
                self.parseInitResponse(response);
            };

        players.forEach(function (_player) {
            let player = new Player(_player.name, _player.isCPU || false, battlefieldSize);

            self.players.push(player);
            self.$html.append(player.$html);

            requestData.data.push({
                player: player.getJSON(),
                battlefield: player.battlefield.getJSON(),
                cells: player.battlefield.cellContainer.getJSON()
            });
        });

        this.request('POST', this.$html.attr('data-init-link'), requestData, onSuccess);
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
        this.setId(response.id);

        let self = this;

        response.battlefields.forEach(function (battlefield) {
            let player = self.findPlayerByName(battlefield.player.name);

            if (undefined !== player) {
                player.setId(battlefield.player.id);

                Object.keys(battlefield.cells).forEach(function (index) {
                    let _cell = battlefield.cells[index],
                        cell  = self.findCell({ playerId: player.id, coordinate: _cell.coordinate });

                    if (undefined !== cell) {
                        cell.setId(_cell.id)
                            .setState(_cell.flags);
                    }
                });
            }
        });
    }

    /**
     * @param {Element} el
     */
    update(el) {
        let cell = this.findCell({ id: el.getAttribute('data-id') });
        if (undefined !== cell) {
            this.cellSend(cell);
        }
    }

    /**
     * @param id {number}
     *
     * @returns {Player|undefined}
     */
    findPlayerById(id) {
        return this.players.find(player => player.id == id);
    }

    /**
     * @param name {string}
     *
     * @returns {Player|undefined}
     */
    findPlayerByName(name) {
        return this.players.find(player => player.name == name);
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
        let self = this;

        response.cells.forEach(function (_cell) {
            let cell = self.findCell({ id: _cell.id });

            if (undefined !== cell) {
                cell.setState(_cell.flags);
            }
        });

        if (undefined !== response.result) {
            let text   = this.constructor.resources.config.text,
                player = this.findPlayerById(response.result.player.id);

            if (undefined !== player) {
                player.isHuman()
                    ? this.alertMgr.show(text.win, 'success')
                    : this.alertMgr.show(text.loss, 'error');
            }
        }
    }

    /**
     * @param {{playerId: {number}, id: {number}, coordinate: {string}}} criteria
     *
     * @returns {Cell}
     */
    findCell(criteria) {
        for (let player of this.players) {
            if (undefined !== criteria.playerId && criteria.playerId !== player.id) {
                continue;
            }

            let cell = player.battlefield.findCell(criteria);
            if (undefined !== cell) {
                return cell;
            }
        }
    }

    modalGameInitiation() {
        this.alertMgr.hide();
        this.modalMgr.updateHTML(Game.resources.html.modal).show();

        return this;
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
