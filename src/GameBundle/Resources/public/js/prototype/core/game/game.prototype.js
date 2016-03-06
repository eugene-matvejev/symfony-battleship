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

    this.id       = 'undefined';
    this.players  = [];
}

/**
 * @property {int}      id
 * @property {Player[]} players
 */
Game.prototype = {
    ///**
    // * @type {int|string}
    // */
    //id: 'undefined',
    /**
     * @param {int} id
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
     * @param {int} battlefieldSize
     */
    init: function(players, battlefieldSize) {
        this.id      = 'undefined';
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
    parseInitResponse: function(response) {
        this.setId(response.id);
        let self = this;

        response.data.map(function(el) {
            let player  = self.findPlayerByName(el.player.name);

            if (undefined !== player) {
                player.setId(el.player.id);
            }
        });
        //for (let i in response.data) {
        //    let _player = response.data[i].player,
        //        player  = this.findPlayerByName(_player.name);
        //
        //    if (player instanceof Player) {
        //        player.setId(_player.id);
        //    }
        //}
    },
    update: function(el) {
        let config = Player.resources.config,
            pid = el.parentElement.parentElement.parentElement.getAttribute(config.attribute.id);
        let player = this.findPlayerById(pid);

        if(player instanceof Player && player.type !== config.type.human) {
            let attr = Cell.resources.config.attribute,
                cell = this.findCell(player.id, parseInt(el.getAttribute(attr.xAxis)), parseInt(el.getAttribute(attr.yAxis)));

            if(cell instanceof Cell) {
                this.cellSend({game: this.getJSON(), player: player.getJSON(), cell: cell.getJSON()});
            }
        }
    },
    findPlayerById: function(id) {
        return this.players.find(el => el.id == id);
    },
    findPlayerByName: function(name) {
        return this.players.find(el => el.name == name);
    },
    findHumanPlayer: function() {
        return this.players.find(el => el.isHuman());
    },
    cellSend: function(cell) {
        var self = this;

        this.apiMgr.request('PATCH', this.$html.attr(Game.resources.config.route.turn), cell,
            function(response) {
                self.parseUpdateResponse(response);
            },
            function(response) {

            }
        );

    },
    parseUpdateResponse: function(response) {
        var _config = Game.resources.config;
        for(var index in response) {
            if(index ===  _config.json.victory) {
                response[index].player.id != this.findHumanPlayer().id
                    ? this.alertMgr.show(_config.text.loss, AlertMgr.resources.config.type.error)
                    : this.alertMgr.show(_config.text.win, AlertMgr.resources.config.type.success);
            } else {
                var battlefield = response[index];
                for(var subIndex in battlefield) {
                    var cell = this.findCell(battlefield[subIndex].player.id, battlefield[subIndex].x, battlefield[subIndex].y);
                    if(cell instanceof Cell) {
                        cell.setState(battlefield[subIndex].s);
                    }
                }
            }

        }
    },
    /**
     * @param {int} pid
     * @param {int} x
     * @param {int} y
     *
     * @returns {Cell|undefined}
     */
    findCell: function(pid, x, y) {
        let player = this.findPlayerById(pid);

        if (player instanceof Player) {
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
    trigger: {
        player: 'player-nickname',
        bfsize: 'game-battlefield-size'
    },
    text: {
        win: 'you won',
        loss: 'you lost'
    },
    limits: {
        minBFSize: 5,
        maxBFSize: 15,
        namePattern: /^[a-zA-Z0-9\.\-\ \@]{1,255}$/
    },
    route: {
        turn: 'data-turn-link',
        init: 'data-init-link'
    },
    json: {
        victory: 'victory'
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
