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
     * @param {int|string} id
     *
     * @returns {Game}
     */
    setId(id) {
        this.id = id;

        return this;
    }

    /**
     * @returns {{id: {int}}}
     */
    getJSON() {
        return { id: this.id };
    }

    /**
     * @param {{name: {string}, isCPU: {boolean}}[]} players
     * @param {int}                                  battlefieldSize
     */
    init(players, battlefieldSize) {
        super.pageMgr.switchSection(document.querySelector('.page-sidebar li[data-section="game-current-area"]'));

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

        players.map(function (_player) {
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
     *     id: {int},
     *     battlefields: {
     *         id: {int},
     *         player: {id: {int}, name: {string}},
     *         cells: {id: {int}, coordinate: {string}, state: {id: {int}}}
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
                        cell.setId(_cell.id);
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
     * @param id {int}
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
     * @param {{cells: {id: {int}, state: {id: {int}}}[], result: {player: {Object}}}} response
     */
    parseUpdateResponse(response) {
        let self = this;

        response.cells.forEach(function (_cell) {
            let cell = self.findCell({ id: _cell.id });

            if (undefined !== cell) {
                cell.setState(_cell.state.id);
            }
        });

        if (undefined !== response.result) {
            let text   = Game.resources.config.text,
                type   = AlertMgr.resources.config.type,
                player = this.findPlayerById(response.result.player.id);

            if (undefined !== player) {
                player.isHuman()
                    ? this.alertMgr.show(text.win, type.success)
                    : this.alertMgr.show(text.loss, type.error);
            }
        }
    }

    /**
     * @param {{playerId: {int}, id: {int}, coordinate: {string}}} criteria
     *
     * @returns {Cell}
     */
    findCell(criteria) {
        for (let i = 0; i < this.players.length; i++) {
            if (undefined !== criteria.playerId && criteria.playerId !== this.players[i].id) {
                continue;
            }

            let cell = this.players[i].battlefield.findCell(criteria);

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

    modalUnlockSubmission() {
        this.modalMgr.unlockSubmission(false);

        let validation             = Game.resources.validation,
            isUsernameValid        = validation.validateInput(document.getElementById('model-input-player-name')),
            isBattlefieldSizeValid = validation.validateInput(document.getElementById('model-input-battlefield-size'));

        if (isUsernameValid && isBattlefieldSizeValid) {
            this.modalMgr.unlockSubmission(true);
        }
    }
}

Game.resources            = {};
Game.resources.config     = {
    /** @enum {string} */
    text: {
        win: 'you won',
        loss: 'you lost'
    },
    pattern: {
        /** @enum {int} */
        battlefield: {
            min: 7,
            max: 12
        },
        /** @type {Object} */
        username: /^[a-zA-Z0-9\.\- @]{1,100}$/
    }
};
Game.resources.validation = {
    /**
     * @param {Element} el
     *
     * @returns {boolean}
     */
    validateInput(el) {
        switch (el.id) {
            case 'model-input-player-name':
                if (!Game.resources.config.pattern.username.test(el.value)) {
                    el.value = el.value.substr(0, el.value.length - 1);

                    return false;
                }
                return true;
            case 'model-input-battlefield-size':
                let battlefieldSize = Game.resources.config.pattern.battlefield
                if (isNaN(el.value))
                    el.value = el.value.substr(0, el.value.length - 1);
                else if (el.value > battlefieldSize.max)
                    el.value = battlefieldSize.max;

                return el.value >= battlefieldSize.min;
        }
    }
};
Game.resources.html       = {
    /**
     * @returns {string}
     */
    modal: function () {
        let battlefield = Game.resources.config.pattern.battlefield;

        return ' \
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
                                <label for="model-input-player-name">nickname</label> \
                                <input type="text" class="form-control" id="model-input-player-name" placeholder=""> \
                            </div> \
                            <div class="form-group"> \
                                <label for="model-input-battlefield-size">battlefield size</label> \
                                <input type="test" class="form-control" id="model-input-battlefield-size" \
                                    placeholder="between ' + battlefield.min + ' and ' + battlefield.max + '"> \
                            </div> \
                        </div> \
                        <div class="modal-footer"> \
                            <button type="button" id="model-button-init-new-game" class="btn btn-primary" disabled="disabled">next step</button> \
                        </div> \
                    </div> \
                </div> \
            </div>';
    }
};
