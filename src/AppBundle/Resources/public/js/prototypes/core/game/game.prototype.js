function Game() {
    this.apiMgr   = new APIMgr();
    this.pageMgr  = new PageMgr();
    this.alertMgr = new AlertMgr();
    this.modalMgr = new ModalMgr();
    this.$area    = $('#game-area');
    this.initModalModule();
}

Game.prototype = {
    id: 'undefined',
    players: [],
    json: {},
    update: function(el) {
        var playerId  = el.parentElement.parentElement.parentElement.getAttribute(Player.tag.id),
            player    = this.getPlayerById(playerId),
            cellX     = el.getAttribute(Cell.tag.x),
            cellY     = el.getAttribute(Cell.tag.y),
            cell      = this.getCellData(playerId, cellX, cellY);

        if(player.typeof !== Player.typeof.human && cell instanceof Cell)
            this.sendCell({x: cell.x, y: cell.y, game: {id: this.id, player: {id: playerId, type: player.typeof}}});
    },
    wipeHTML: function() {
        this.$area.html('');
    },
    updateHTML: function() {
        this.wipeHTML();

        for(var i in this.players) {
            this.players[i].updateHTML();
        }
    },
    getCellData: function(playerId, x, y) {
        var player = this.getPlayerById(playerId);
        if(player instanceof Player) {
            var cell = player.battlefield.getCellData(x, y);
            if(cell instanceof Cell)
                return cell;
        }
        return undefined;
    },
    getPlayerById: function(id) {
        for(var i in this.players) {
            if(this.players[i].id == id)
                return this.players[i];
        }
        return undefined;
    },
    getPlayerByName: function(name) {
        for(var i in this.players) {
            if(this.players[i].name == name)
                return this.players[i];
        }
        return undefined;
    },
    sendCell: function(cell) {
        this.pageMgr.loadingMode(true);
        var self = this;
        this.apiMgr.request(this.$area.attr(Game.indexes.resource.link.turn), 'POST', JSON.stringify(cell),
            function(json) {
                self.updateCells(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    init: function(players, battlefieldSize) {
        this.wipeHTML();
        this.players = [];
        this.id = 'undefined';

        this.pageMgr.switchSection(document.querySelector('.page-sidebar li[data-section="game-area"]'));

        for(var i in players) {
            this.players.push((new Player(this.$area, players[i].name == 'CPU' ? true : undefined))
                                .setName(players[i].name)
                                .initBattlefield(battlefieldSize)
            );
        }

        var gameJSON = {
            id: this.id,
            data: []
        };

        for(var i in this.players) {
            var player = this.players[i],
                cells  = player.battlefield.cells.data,
                json   = {
                    player: {id: player.id, name: player.name, type: player.typeof},
                    battlefield: {id: player.battlefield.id},
                    cells: []
                };

            for(var j in cells) {
                for(var k in cells[j]) {
                    json.cells.push(cells[j][k]);
                }
            }
            gameJSON.data.push(json);
        }

        this.json = gameJSON;
        var self = this;

        this.apiMgr.request(this.$area.attr(Game.indexes.resource.link.init), 'POST', JSON.stringify(gameJSON),
            function(json) {
                self.updateEntireData(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    setId: function(id) {
        this.id = id;
        return this;
    },
    updateEntireData: function(json) {
        for(var i in json.data) {
            this.getPlayerByName(json.data[i].player.name)
                .setId(json.data[i].player.id);
        }
        this.json = json;
        this.name = json.name;
        this.id   = json.id;
        this.updateHTML();

        this.pageMgr.loadingMode(false);
    },
    updateCells: function(json) {
        for(var i in json) {
            if(i ==  Game.indexes.json.victory) {
                json[i].pid != this.getHumanPlayer().id
                    ? this.alertMgr.show('VICTORY', AlertMgr.type.success)
                    : this.alertMgr.show('LOOSER', AlertMgr.type.error);
            } else this.updateCellDataByPlayer(json[i]);
        }

        this.updateHTML();
    },
    getHumanPlayer: function() {
        for(var i in this.players) {
            if(this.players[i].typeof == Player.typeof.human)
                return this.players[i];
        }

        return undefined;
    },
    updateCellDataByPlayer: function(json) {
        for(var i in this.players) {
            if(this.players[i].id == json.pid) {
                var cells = this.players[i].battlefield.cells.data;
                for(var j in cells) {
                    for(var k in cells[j]) {
                        var cell = cells[j][k];
                        if(cell.x == json.x && cell.y == json.y) {
                            cell.setState(json.s);
                            return;
                        }
                    }
                }
            }
        }
    },
    initModalModule: function() {
        this.modal.alertMgr = this.alertMgr;
        this.modal.modalMgr = this.modalMgr;

        return this;
    },
    modal: {
        alertMgr: undefined,
        modalMgr: undefined,
        initGame: function() {
            this.alertMgr.hide();
            this.modalMgr.updateHTML(this.getHTML()).show();

            return this;
        },
        getHTML: function() {
            return $($.parseHTML(
                '<div class="modal fade">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">' +
                            '<div class="modal-header">' +
                                '<button type="button" class="close" data-dismiss="modal">' +
                                    '<span aria-hidden="true">&times;</span>' +
                                '</button>' +
                                '<h4 class="modal-title">нour details</h4>' +
                            '</div>' +
                            '<div class="modal-body">' +
                                '<div class="form-group">' +
                                    '<label for="' + Game.indexes.modal.playerName + '">your nickname</label>' +
                                    '<input type="text" class="form-control" id="' + Game.indexes.modal.playerName + '" placeholder="">' +
                                '</div>' +
                                '<div class="form-group">' +
                                    '<label for="' + Game.indexes.modal.battlefieldSize + '">battlefiend size</label>' +
                                    '<input type="test" class="form-control" id="' + Game.indexes.modal.battlefieldSize + '" placeholder="between 5 and 25">' +
                                '</div>' +
                            '</div>' +
                            '<div class="modal-footer">' +
                                '<button type="button" id="new-game-btn" class="btn btn-primary" disabled="disabled">next step</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            ));
        },
        validate: function(el) {
            switch(el.id) {
                case Game.indexes.modal.playerName:
                    if(el.value.length > 0 && !Game.limits.playerNameRegex.test(el.value)) {
                        el.value = el.value.substr(0, el.value.length - 1);
                        return false;
                    }
                    return true;
                case Game.indexes.modal.battlefieldSize:
                    if(el.value.length > 0 && isNaN(el.value))
                        el.value = el.value.substr(0, el.value.length - 1);
                    else if(el.value.length > 1 && el.value < Game.limits.minBattlefieldSize)
                        el.value = Game.limits.minBattlefieldSize;
                    else if(el.value.length > 2 || el.value > Game.limits.maxBattlefieldSize)
                        el.value = Game.limits.maxBattlefieldSize;
                    else
                        return true;
                    return false;
            }
        },
        unlockSubmition: function() {
            this.modalMgr.disableSubmision();
            var playerName      = document.getElementById(Game.indexes.modal.playerName),
                battlefieldSize = document.getElementById(Game.indexes.modal.battlefieldSize);

            if(this.validate(playerName) && this.validate(battlefieldSize) && battlefieldSize.value > Game.limits.minBattlefieldSize) {
                this.modalMgr.enableSubmision();
            }
        }
    }
};

Game.indexes = {
    modal: {
        playerName: 'player-nickname',
        battlefieldSize: 'game-battlefield-size'
    },
    json: {
        victory: 'victory'
    },
    resource: {
        link: {
            turn: 'data-turn-link',
            init: 'data-init-link'
        }
    }
};
Game.limits = {
    minBattlefieldSize: 5,
    maxBattlefieldSize: 20,
    playerNameRegex: /^[a-zA-Z0-9\.\-\ \@]+$/
};
