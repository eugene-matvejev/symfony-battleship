'use strict';

/**
 * @constructor
 */
function Game() {
    this.$html    = $('#game-area');
    this.apiMgr   = new APIMgr();
    this.pageMgr  = new PageMgr();
    this.alertMgr = new AlertMgr();
    this.modalMgr = new ModalMgr();

    this.setId('undefined');
    this.players  = [];
}

/**
 * @property {jQuery}   $html
 * @property {int}      id
 * @property {Player[]} players
 */
Game.prototype = {
    /**
     * @param {int|string} id
     *
     * @returns {Game}
     */
    setId: function(id) {
        this.id = id;

        return this;
    },
    /**
     * @returns {{id: {int}}}
     */
    getJSON: function() {
        return {id: this.id};
    },
    /**
     * @param {{name: {string}, isCPU: {boolean}}[]} players
     * @param {int}                                  battlefieldSize
     */
    init: function(players, battlefieldSize) {
        this.setId('undefined');
        this.players = [];
        this.pageMgr.switchSection(document.querySelector('.page-sidebar li[data-section="' + PageMgr.resources.config.section.game + '"]'));

        this.$html.html('');
        let self = this,
            requestData = {
                id: this.getJSON(),
                data: []
            };

        players.map(function(el) {
            let player = new Player(self.$html, el.name, el.isCPU || false, battlefieldSize);
            self.players.push(player);

            requestData.data.push({
                player: player.getJSON(),
                battlefield: player.battlefield.getJSON(),
                cells: player.battlefield.cellContainer.getJSON()
            });
        });

        this.apiMgr.request('POST', this.$html.attr(Game.resources.config.route.init), requestData,
            function(response) {
                self.parseInitResponse(response);
            },
            function(response) {
            }
        );
    },
    /**
     * @param {{id: {int}, data: []}} response
     */
    parseInitResponse: function(response) {
        this.setId(response.id);
        let self = this;

        response.data.map(function(el) {
            let player  = self.findPlayerByName(el.player.name);

            if (undefined !== player) {
                player.setId(el.player.id);
            }
        });
    },
    /**
     * @param {Element} el
     */
    update: function(el) {
        let config = Player.resources.config,
            pid = el.parentElement.parentElement.parentElement.getAttribute(config.attribute.id),
            player = this.findPlayerById(pid);

        if (undefined !== player && player.isCPU()) {
            let attr = Cell.resources.config.attribute,
                cell = this.findCell(player.id, parseInt(el.getAttribute(attr.xAxis)), parseInt(el.getAttribute(attr.yAxis)));

            if (undefined !== cell) {
                this.cellSend({game: this.getJSON(), player: player.getJSON(), cell: cell.getJSON()});
            }
        }
    },
    /**
     * @param id {int}
     *
     * @returns {Player|undefined}
     */
    findPlayerById: function(id) {
        return this.players.find(el => el.id == id);
    },
    /**
     * @param name {string}
     *
     * @returns {Player|undefined}
     */
    findPlayerByName: function(name) {
        return this.players.find(el => el.name == name);
    },
    /**
     * @param {{game: {Object}, player: {Object}, cell: {Object}}} requestData
     */
    cellSend: function(requestData) {
        var self = this;

        this.apiMgr.request('PATCH', this.$html.attr(Game.resources.config.route.turn), requestData,
            function(response) {
                self.parseUpdateResponse(response);
            },
            function(response) {

            }
        );
    },
    /**
     * @param {{cells: [], victory: {Object}}} response
     */
    parseUpdateResponse: function(response) {
        let self = this;

        response.cells.map(function(el) {
            let cell = self.findCell(el.player.id, el.x, el.y);

            if (undefined !== cell) {
                cell.setState(el.s);
            }
        });

        if (undefined !== response.victory) {
            let text = Game.resources.config.text,
                type = AlertMgr.resources.config.type,
                player = this.findPlayerById(response.victory.player.id);

            if (undefined !== player) {
                player.isHuman()
                    ? this.alertMgr.show(text.win, type.success)
                    : this.alertMgr.show(text.loss, type.error);
            }
        }
    },
    /**
     * @param {int} playerId
     * @param {int} x
     * @param {int} y
     *
     * @returns {Cell|undefined}
     */
    findCell: function(playerId, x, y) {
        let player = this.findPlayerById(playerId);

        if (undefined !== player) {
            return player.battlefield.getCell(x, y);
        }
    },
    modalGameInitiation: function() {
        this.alertMgr.hide();
        this.modalMgr.updateHTML(Game.resources.html.modal).show();

        return this;
    },
    /**
     * @param {Element} el
     *
     * @returns {boolean}
     */
    modalValidateInput: function(el) {
        var _config = Game.resources.config;

        switch(el.id) {
            case _config.trigger.player:
                console.log(el.value);
                if(!_config.limits.namePattern.test(el.value)) {
                    el.value = el.value.substr(0, el.value.length - 1);
                    return false;
                }
                return true;
            case _config.trigger.bfsize:
                if(el.value.length > 0 && isNaN(el.value))
                    el.value = el.value.substr(0, el.value.length - 1);
                else if(el.value.length > 1 && el.value < _config.limits.minBFSize)
                    el.value = _config.limits.minBFSize;
                else if(el.value.length > 2 || el.value > _config.limits.maxBFSize)
                    el.value = _config.limits.maxBFSize;
                return el.value >= _config.limits.minBFSize && el.value <= _config.limits.maxBFSize;
        }
    },
    modalUnlockSubmission: function() {
        this.modalMgr.unlockSubmission(false);

        var trigger = Game.resources.config.trigger,
            player  = document.getElementById(trigger.player),
            bfSize  = document.getElementById(trigger.bfsize);

        if(this.modalValidateInput(player) && this.modalValidateInput(bfSize)) {
            this.modalMgr.unlockSubmission(true);
        }
    }
};

Game.resources = {};
Game.resources.config = {
    /**
     * @enum {string}
     */
    trigger: {
        player: 'player-nickname',
        bfsize: 'game-battlefield-size'
    },
    /**
     * @enum {string}
     */
    text: {
        win: 'you won',
        loss: 'you lost'
    },
    pattern: {
        /**
         * @enum {int}
         */
        battlefield: {
            min: 5,
            max: 15
        },
        /**
         * @enum {Object}
         */
        username: {
            pattern: /^[a-zA-Z0-9\.\-\ \@]{1,100}$/
        }
    },
    /**
     * @enum {string}
     */
    route: {
        turn: 'data-turn-link',
        init: 'data-init-link'
    }
};
Game.resources.html = {
    /**
     * @returns {string}
     */
    modal: function() {
        let config = Game.resources.config;

        return '' +
            '<div class="modal fade">' +
                '<div class="modal-dialog">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<button type="button" class="close" data-dismiss="modal">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '<h4 class="modal-title">your details</h4>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<div class="form-group">' +
                                '<label for="' + config.trigger.player + '">nickname</label>' +
                                '<input type="text" class="form-control" id="' + config.trigger.player + '" placeholder="">' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label for="' + config.trigger.bfsize + '">battlefield size</label>' +
                                '<input type="test" class="form-control" id="' + config.trigger.bfsize + '"' +
                                    ' placeholder="between ' + config.limits.minBFSize + ' and ' + config.limits.maxBFSize + '">' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" id="new-game-btn" class="btn btn-primary" disabled="disabled">next step</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
    }
};
