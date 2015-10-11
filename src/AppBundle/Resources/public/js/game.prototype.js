function Game(players) {
    this.toInit = players;
    this.$area  = $("#battle-area");
}
Game.prototype = {
    players: [],
    id: undefined,
    game: undefined,
    init: function() {
        this.$area.html('');
        for(var i in this.toInit) {
            var player = (new Player())
                            .setId(this.toInit[i].id)
                            .setName(this.toInit[i].name)
                            .setArea(this.$area)
                            .setBattlefield(new Battlefield())
                            .init();
            console.log(player.battlefield);
            this.players.push(player);
        }
        this.startGame();
    },
    update: function(el) {
        var player = el.parentElement.parentElement.getAttribute('data-player-id'),
            x      = el.getAttribute('data-x'),
            y      = el.getAttribute('data-y'),
            state  = el.getAttribute('data-s');

        var cell = this.getCellData(player, x, y);

        console.log(player, x, y, state);
        console.log(cell);

        if(cell instanceof Cell)
            this.sendCell({x: cell.x, y: cell.y, player: player});
    },
    getCellData: function(playerId, x, y) {
        var player = this.getPlayer(playerId);

        return player instanceof Player
            ? player.battlefield.getCellData(x, y)
            : undefined;
    },
    getPlayer: function(playerId) {
        for(var i in this.players) {
            if(this.players[i].id == playerId)
                return this.players[i];
        }
        return undefined;
    },
    sendCell: function(cell) {
        console.clear();
        console.log(cell);
        var self = this;
        $.ajax({
            contentType: 'application/json',
            dataType: 'json',
            method: 'GET',
            url: self.$area.attr('data-turn-link'),
            data: cell,
            success: function(response) {
                console.log('success >>>' , response);
                $("#debug-area").html(response);
            },
            error: function(response) {
                console.log('error >>>' , response);
                $("#debug-area").html(response);
            }
        });
    },
    startGame: function() {
        var json  = [];
        this.game = {id: (this.id !== undefined ? this.id : 'unk'), data: json};

        for(var i in this.players) {
            var player = {};
                player = {player: {id: this.players[i].id, name: this.players[i].name}, cells: []};
                for(var j in this.players[i].battlefield.cells.data) {
                    player.cells.push(this.players[i].battlefield.cells.data[j]);
                }
            json.push(player);
        }
        var serializedJSON = JSON.stringify(this.game);
        console.log(this.game, serializedJSON);

        var self = this;
        $.ajax({
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            method: 'POST',
            url: self.$area.attr('data-start-link'),
            data: serializedJSON,
            success: function(response) {
                console.log('success >>>' , response);
                self.setId(response.id);
                self.updateData(response);
                console.log(self.game);
                $("#debug-area").html(JSON.stringify(response));
            },
            error: function(response) {
                console.log('error >>>' , response);
                $("#debug-area").html(response);
            }
        });
    },
    setId: function(id) {
        if(this.id === undefined && id !== undefined)
            this.id = id;
        return this;
    },
    updateData: function(json) {
        //this.game = JSON.parse(json);
        this.game = json;

        return this;
    }
};