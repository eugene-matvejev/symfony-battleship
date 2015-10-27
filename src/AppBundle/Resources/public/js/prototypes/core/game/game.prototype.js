function Game() {
    this.apiMgr   = new APIMgr();
    this.pageMgr  = new PageMgr();
    this.alertMgr = new AlertMgr();
    this.modalMgr = new ModalMgr();
    this.$area    = $('#game-area');
    this.data     = {};
    this.initSubModules();
}

Game.prototype = {
    id: 'undefined',
    data: undefined,
    setId: function(id) {
        this.id = id;

        return this;
    },
    getJSON: function() {
        return {id: this.id};
    },
    initSubModules: function() {
        this.player.data   = this.data;

        this.cell.player   = this.player;

        this.modal.alertMgr = this.alertMgr;
        this.modal.modalMgr = this.modalMgr;


        this.html.$area    = this.$area;
        this.html.data     = this.data;

        return this;
    },
    init: function(players, battlefieldSize) {
        this.data = {};
        this.id   = 'undefined';
        this.pageMgr.loadingMode(true)
                    .switchSection(document.querySelector('.page-sidebar li[data-section="game-area"]'));
        this.html.wipe();

        var json = {
            id: this.id,
            data: []
        };

        for(var i in players) {
            var _player = (new Player(this.$area, players[i].name == 'CPU' ? true : undefined))
                            .setName(players[i].name)
                            .initBattlefield(battlefieldSize);
            var _cells  = _player.battlefield.cells.data;
            var _json   = {
                player: {id: _player.id, name: _player.name, type: _player.typeof},
                battlefield: {id: _player.battlefield.id},
                cells: []
            };

            for(var j in _cells) {
                for(var k in _cells[j]) {
                    _json.cells.push(_cells[j][k]);
                }
            }

            json.data.push(_json);

            this.data[i] = _player;
        }

        var self = this;
        this.apiMgr.request('POST', this.$area.attr(Game.indexes.resource.link.init), JSON.stringify(json),
            function(json) {
                self.updateAll(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    update: function(el) {
        var playerId  = el.parentElement.parentElement.parentElement.getAttribute(Player.tag.id),
            player    = this.player.getById(playerId);
        if(player instanceof Player && player.typeof !== Player.typeof.human) {
            var cellX = el.getAttribute(Cell.tag.x),
                cellY = el.getAttribute(Cell.tag.y),
                cell  = this.cell.get(player.id, cellX, cellY);

            if(cell instanceof Cell)
                this.cell.send({game: this.getJSON(), player: player.getJSON(), cell: cell.getJSON()});
        }
    },
    updateAll: function(json) {
        for(var i in json.data) {
            var _player = json.data[i].player,
                player  = this.player.getByName(_player.name);
            if(player instanceof Player)
                player.setId(_player.id);
        }
        this.setId(json.id);
        this.html.update();
    },
    player: {
        getById: function(id) {
            for(var i in this.data) {
                if(this.data[i].id == id)
                    return this.data[i];
            }

            return undefined;
        },
        getByName: function(name) {
            for(var i in this.data) {
                if(this.data[i].name == name)
                    return this.data[i];
            }

            return undefined;
        },
        getByType: function(type) {
            for(var i in this.data) {
                if(this.data[i].typeof == type)
                    return this.data[i];
            }

            return undefined;
        }
    },
    cell: {
        send: function(cell) {
            var self = this;

            //this.pageMgr.loadingMode(true);
            this.apiMgr.request('POST', this.$area.attr(this.constructor.indexes.resource.link.turn), JSON.stringify(cell),
                function(json) {
                    self.updateAll(json);
                    self.pageMgr.loadingMode(false);
                }
            );
        },
        updateAll: function(json) {
            for(var i in json) {
                if(i ==  Game.indexes.json.victory) {
                    json[i].pid != this.getHumanPlayer().id
                        ? this.alertMgr.show('VICTORY', AlertMgr.type.success)
                        : this.alertMgr.show('LOOSER', AlertMgr.type.error);
                } else this.updateByPlayer(json[i]);
            }

            this.updateHTML();
        },
        updateByPlayer: function(json) {
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
        get: function(pid, x, y) {
            var player = this.player.getById(pid);
            if(player instanceof Player) {
                var cell = player.battlefield.getCell(x, y);
                if(cell instanceof Cell)
                    return cell;
            }

            return undefined;
        }
    },
    html: {
        wipe: function() {
            this.$area.html('');
        },
        update: function() {
            this.wipe();

            for(var i in this.data) {
                var player = this.data[i];
                player.updateHTML();
            }
        }
    },
    modal: {
        initGame: function() {
            this.alertMgr.hide();
            this.modalMgr.updateHTML(Game.resource.html.modal).show();

            return this;
        },
        validate: function(el) {
            switch(el.id) {
                case Game.resource.config.trigger.player:
                    if(el.value.length > 0 && !Game.resource.config.limits.nameRegex.test(el.value)) {
                        el.value = el.value.substr(0, el.value.length - 1);
                        return false;
                    }
                    return true;
                case Game.resource.config.trigger.bfsize:
                    var limits = Game.resource.config.limits;
                    if(el.value.length > 0 && isNaN(el.value))
                        el.value = el.value.substr(0, el.value.length - 1);
                    else if(el.value.length > 1 && el.value < limits.minBFSize)
                        el.value = limits.minBFSize;
                    else if(el.value.length > 2 || el.value > limits.maxBFSize)
                        el.value = limits.maxBFSize;
                    else
                        return true;
                    return false;
            }
        },
        unlockSubmition: function() {
            this.modalMgr.disableSubmision();
            var config = Game.resource.config,
                player  = document.getElementById(config.trigger.player),
                bfSize  = document.getElementById(config.trigger.bfsize);

            if(this.validate(player) && this.validate(bfSize) && bfSize.value > config.limits.minBFSize) {
                this.modalMgr.enableSubmision();
            }
        }
    }
};
Game.resource = {};
Game.resource.config = {
    trigger: {
        player: 'player-nickname',
        bfsize: 'game-battlefield-size'
    },
    limits: {
        minBFSize: 5,
        maxBFSize: 20,
        nameRegex: /^[a-zA-Z0-9\.\-\ \@]+$/
    },
    route: {
        turn: 'data-turn-link',
        init: 'data-init-link'
    },
    json: {
        victory: 'victory'
    }
};
Game.resource.html = {
    modal: '<div class="modal fade">' +
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
                                '<label for="' + Game.resource.config.trigger.player + '">your nickname</label>' +
                                '<input type="text" class="form-control" id="' + Game.resource.config.trigger.player + '" placeholder="">' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label for="' + Game.resource.config.trigger.bfsize + '">battlefiend size</label>' +
                                '<input type="test" class="form-control" id="' + Game.resource.config.trigger.bfsize + '" placeholder="between 5 and 25">' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" id="new-game-btn" class="btn btn-primary" disabled="disabled">next step</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
};
