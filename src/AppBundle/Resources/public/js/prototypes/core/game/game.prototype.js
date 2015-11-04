function Game() {
    this.apiMgr   = new APIMgr();
    this.pageMgr  = new PageMgr();
    this.alertMgr = new AlertMgr();
    this.modalMgr = new ModalMgr();
    this.$area    = $('#game-area');
    this.initSubModules();
}

Game.prototype = {
    id: 'undefined',
    data: {},
    setId: function(id) {
        this.id = id;

        return this;
    },
    getJSON: function() {
        return {id: this.id};
    },
    initSubModules: function() {
        this.html.super =
        this.cell.super =
        this.modal.super =
        this.player.super = this;

        return this;
    },
    init: function(players, battlefieldSize) {
        this.data = {};
        this.id   = 'undefined';
        //this.modalMgr.hide();
        this.pageMgr.loadingMode(true);
        this.pageMgr.switchSection(document.querySelector('.page-sidebar li[data-section="' + PageMgr.resource.config.section.game + '"]'));

        this.html.wipe();
        var json = {
            id: this.id,
            data: []
        };

        for(var i in players) {
            var player = (new Player(this.$area, players[i].name, players[i].name == 'CPU' ? true : undefined))
                                    .initBattlefield(battlefieldSize);
            var cells  = player.battlefield.cells.data;
            var _json  = {
                player: {id: player.id, name: player.name, type: player.type},
                battlefield: {id: player.battlefield.id},
                cells: []
            };

            for(var j in cells) {
                for(var k in cells[j]) {
                    _json.cells.push(cells[j][k]);
                }
            }

            json.data.push(_json);

            this.data[i] = player;
        }

        console.log(json);
        var self = this;
        this.apiMgr.request('POST', this.$area.attr(Game.resource.config.route.init), JSON.stringify(json),
            function(json) {
                //self.modalMgr.hide();
                //debugger;
                console.log(json);
                self.updateAll(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    update: function(el) {
        var playerId  = el.parentElement.parentElement.parentElement.getAttribute(Player.resource.config.trigger.id),
            player    = this.player.getById(playerId);
        if(player instanceof Player && player.type !== Player.resource.config.type.human) {
            var cellX = el.getAttribute(Cell.tag.x),
                cellY = el.getAttribute(Cell.tag.y),
                cell  = this.cell.get(player.id, cellX, cellY);

            if(cell instanceof Cell)
                this.cell.send({game: this.getJSON(), player: player.getJSON(), cell: cell.getJSON()});
        }
    },
    updateAll: function(json) {
        for(var i in json.data) {
            /**
             $std = new \stdClass();
             $std->id = $game->getId();
             $std->data = [];
             foreach($game->getBattlefields() as $battlefield) {
            $_json = new \stdClass();
            $_json->id = $battlefield->getId();
            $_json->player = PlayerModel::getJSON($battlefield->getPlayer());
            $_json->cells = [];

            foreach($battlefield->getCells() as $cell) {
                $_json->cells[] = CellModel::getJSON($cell, true);
            }

            $std->data[] = $_json;
        }

             return $std;             */
            var _player = json.data[i].player,
                player  = this.player.getByName(_player.name);
            if(player instanceof Player)
                player.setId(_player.id);
        }

        this.setId(json.id);
        this.html.update();
    },
    player: {
        findByCriteria: function(criteria, value) {
            for(var i in this.super.data) {
                if(this.super.data[i][criteria] == value)
                    return this.super.data[i];
            }
        },
        getById: function(id) {
            return this.findByCriteria('id', id);
        },
        getByName: function(name) {
            return this.findByCriteria('name', name);
        },
        getByType: function(type) {
            return this.findByCriteria('type', type);
        },
        getHuman: function() {
            return this.getByType(Player.resource.config.type.human);
        }
    },
    cell: {
        send: function(cell) {
            var self = this;

            this.super.pageMgr.loadingMode(true);
            this.super.apiMgr.request('POST', this.super.$area.attr(Game.resource.config.route.turn), JSON.stringify(cell),
                function(json) {
                    self.super.pageMgr.loadingMode(false);
                    self.update(json);
                }
            );

        },
        update: function(json) {
            for(var i in json) {
                if(i ==  Game.resource.config.json.victory) {
                    json[i].pid != this.super.player.getHuman().id
                        ? this.super.alertMgr.show('VICTORY', AlertMgr.resource.config.type.success)
                        : this.super.alertMgr.show('LOOSER', AlertMgr.resource.config.type.error);
                } else {
                    var cell = this.get(json[i].pid, json[i].x, json[i].y);
                    if(cell instanceof Cell) {
                        cell.setState(json[i].s)
                    }
                }

            }
            this.super.html.update();

        },
        get: function(pid, x, y) {
            var player = this.super.player.getById(pid);
            if(player instanceof Player) {
                var cell = player.battlefield.getCell(x, y);
                if(cell instanceof Cell)
                    return cell;
            }
        }
    },
    html: {
        wipe: function() {
            this.super.$area.html('');
        },
        update: function() {
            this.wipe();

            for(var i in this.super.data) {
                var player = this.super.data[i];
                player.updateHTML();
            }
        }
    },
    modal: {
        initGame: function() {
            this.super.alertMgr.hide();
            this.super.modalMgr.updateHTML(Game.resource.html.modal).show();

            return this;
        },
        validate: function(el) {
            var limits = Game.resource.config.limits;
            switch(el.id) {
                case Game.resource.config.trigger.player:
                    if(el.value.length > 0 && !limits.nameRegex.test(el.value)) {
                        el.value = el.value.substr(0, el.value.length - 1);
                        return false;
                    }
                    return true;
                case Game.resource.config.trigger.bfsize:
                    if(el.value.length > 0 && isNaN(el.value))
                        el.value = el.value.substr(0, el.value.length - 1);
                    else if(el.value.length > 1 && el.value < limits.minBFSize)
                        el.value = limits.minBFSize;
                    else if(el.value.length > 2 || el.value > limits.maxBFSize)
                        el.value = limits.maxBFSize;
                    return el.value >= limits.minBFSize && el.value <= limits.maxBFSize;
            }
        },
        unlockSubmition: function() {
            this.super.modalMgr.disableSubmision();
            var config = Game.resource.config,
                player  = document.getElementById(config.trigger.player),
                bfSize  = document.getElementById(config.trigger.bfsize);

            if(this.validate(player) && this.validate(bfSize)) {
                this.super.modalMgr.enableSubmision();
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
