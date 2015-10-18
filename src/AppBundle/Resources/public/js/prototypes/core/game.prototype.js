function Game(players) {
    this.$area = $('#game-area');

    for(var i in players) {
        var player = (new Player(this.$area, players[i].name == 'CPU' ? true : undefined))
            .setName(players[i].name);
        this.players.push(player);
    }
    this.init();
}

Game.prototype = {
    id: 'undefined',
    players: [],
    json: {},
    update: function(el) {
        var playerId  = el.parentElement.parentElement.parentElement.getAttribute(Player.tag.id),
            cellX     = el.getAttribute(Cell.tag.x),
            cellY     = el.getAttribute(Cell.tag.y),
            cellState = el.getAttribute(Cell.tag.state);

        var player = this.getPlayerById(playerId),
            cell   = this.getCellData(playerId, cellX, cellY);

        console.log(playerId, player, cellX, cellY, cellState, cell);

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
        var self = this;
        console.log(JSON.stringify(cell));
        $.ajax({
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            method: 'POST',
            url: self.$area.attr('data-turn-link'),
            data: JSON.stringify(cell),
            success: function(response) {
                $("#debug-area").html(JSON.stringify(response));
                self.updateCells(response);
            },
            error: function(response) {
                $("#debug-area").html(response.responseText);
            }
        });
    },
    init: function() {
        var gameJSON = {
            id: (this.id !== undefined ? this.id : 'unk'),
            name: (this.name !== undefined ? this.name : 'unk'),
            data: []
        };

        for(var i in this.players) {
            var player = this.players[i];
            console.log(player);
                var cells  = player.battlefield.cells.data,
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
        var serializedJSON = JSON.stringify(gameJSON),
            self = this;

        console.log(serializedJSON);
        $.ajax({
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            method: 'POST',
            url: self.$area.attr('data-start-link'),
            data: serializedJSON,
            success: function(response) {
                self.updateEntireData(response);
                $("#debug-area").html(JSON.stringify(response));
            },
            error: function(response) {
                $("#debug-area").html(response.responseText);
            }
        });
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

        (new PageMgr())
            .loadingMode(false);
    },
    updateCells: function(json) {
        for(var i in json) {
            this.updateCellDataByPlayer(json[i])
        }
        this.updateHTML();
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
    }
};