function Game() {
    this.apiMgr   = new APIMgr();
    this.pageMgr  = new PageMgr();
    this.alertMgr = new AlertMgr();
    this.modalMgr = new ModalMgr();
    this.$area    = $('#game-area');
}

Game.prototype = {
    id: 'undefined',
    data: [],
    setId: function(id) {
        this.id = id;

        return this;
    },
    getJSON: function() {
        return {id: this.id};
    },
    init: function(players, battlefieldSize) {
        this.data = [];
        this.id   = 'undefined';
        this.pageMgr.loadingMode(true);
        this.pageMgr.switchSection(document.querySelector('.page-sidebar li[data-section="' + PageMgr.resources.config.section.game + '"]'));

        this.htmlWipe();
        var json = {
            id: this.id,
            data: []
        };

        for(var i in players) {
            var player = (new Player(this.$area, players[i].name, players[i].name == 'CPU' ? true : undefined, battlefieldSize)),
                cells  = player.battlefield.cells.data,
                _json  = {
                    player: {id: player.id, name: player.name, type: player.type},
                    battlefield: {id: player.battlefield.id},
                    cells: []
                };

            for(var x in cells) {
                for(var y in cells[x]) {
                    _json.cells.push(cells[x][y]);
                }
            }

            json.data.push(_json);

            this.data.push(player);
        }

        var self = this;
        this.apiMgr.request('POST', this.$area.attr(Game.resources.config.route.init), JSON.stringify(json),
            function(json) {
                self.parseInitResponse(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    parseInitResponse: function(json) {
        for(var i in json.data) {
            var _player = json.data[i].player,
                player  = this.findPlayerByName(_player.name);
            if(player instanceof Player)
                player.setId(_player.id);
        }

        this.setId(json.id);
        this.htmlUpdate();
    },
    htmlWipe: function() {
        this.$area.html('');
    },
    htmlUpdate: function() {
        //for(var i in this.data) {
        //    this.data[i].player.htmlUpdate();
        //}
    },
    updateGame: function(el) {
        var _config = Player.resources.config,
            player  = this.findPlayerById(el.parentElement.parentElement.parentElement.getAttribute(_config.trigger.id));
        if(player instanceof Player && player.type !== _config.type.human) {
                _config = Cell.resources.config;
            var cell    = this.cellGet(player.id, el.getAttribute(_config.html.attr.x), el.getAttribute(_config.html.attr.y));

            if(cell instanceof Cell)
                this.cellSend({game: this.getJSON(), player: player.getJSON(), cell: cell.getJSON()});
        }
    },
    findByPlayerCriteria: function(criteria, value) {
        for(var i in this.data) {
            if(this.data[i][criteria] == value)
                return this.data[i];
        }
    },
    findPlayerById: function(id) {
        return this.findByPlayerCriteria('id', id);
    },
    findPlayerByName: function(name) {
        return this.findByPlayerCriteria('name', name);
    },
    findPlayerByType: function(type) {
        return this.findByPlayerCriteria('type', type);
    },
    findHumanPlayer: function() {
        return this.findPlayerByType(Player.resources.config.type.human);
    },
    cellSend: function(cell) {
        var self = this;

        this.pageMgr.loadingMode(true);
        this.apiMgr.request('POST', this.$area.attr(Game.resources.config.route.turn), JSON.stringify(cell),
            function(json) {
                self.pageMgr.loadingMode(false);
                self.cellUpdate(json);
            }
        );

    },
    cellUpdate: function(json) {
        var _config = Game.resources.config;
        for(var i in json) {
            if(i ==  _config.json.victory) {
                json[i].pid != this.findHumanPlayer().id
                    ? this.alertMgr.show(_config.text.win, AlertMgr.resources.config.type.success)
                    : this.alertMgr.show(_config.text.loss, AlertMgr.resources.config.type.error);
            } else {
                var cell = this.cellGet(json[i].pid, json[i].x, json[i].y);
                if(cell instanceof Cell) {
                    cell.setState(json[i].s)
                }
            }

        }
        this.htmlUpdate();
    },
    cellGet: function(pid, x, y) {
        var player = this.findPlayerById(pid);
        if(player instanceof Player) {
            var cell = player.battlefield.getCell(x, y);
            if(cell instanceof Cell)
                return cell;
        }
    },
    modalGameInitiation: function() {
        this.alertMgr.hide();
        this.modalMgr.updateHTML(Game.resources.html.modal).show();

        return this;
    },
    modalValidateInput: function(el) {
        var _config = Game.resources.config;
        //debugger;

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
    modalUnlockSubmition: function() {
        this.modalMgr.unlockSubmision(false);

        var _config = Game.resources.config,
            player  = document.getElementById(_config.trigger.player),
            bfSize  = document.getElementById(_config.trigger.bfsize);

        if(this.modalValidateInput(player) && this.modalValidateInput(bfSize)) {
            this.modalMgr.unlockSubmision(true);
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
        maxBFSize: 20,
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
                                '<label for="' + Game.resources.config.trigger.player + '">your nickname</label>' +
                                '<input type="text" class="form-control" id="' + Game.resources.config.trigger.player + '" placeholder="">' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label for="' + Game.resources.config.trigger.bfsize + '">battlefiend size</label>' +
                                '<input type="test" class="form-control" id="' + Game.resources.config.trigger.bfsize + '" placeholder="between 5 and 25">' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" id="new-game-btn" class="btn btn-primary" disabled="disabled">next step</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
};
