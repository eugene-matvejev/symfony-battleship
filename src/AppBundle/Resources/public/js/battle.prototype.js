function Battle(players) {
    this.toInit = players;
    this.$area  = $("#battle-area");
}
Battle.prototype = {
    players: [],
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
        console.log(cell)
        var self = this,
            ajax = $.ajax({
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
        //$("#debug-area").html('reponse').append(ajax.responseText);
    },
    startGame: function() {
        var arr = [];
        for(var i in this.players) {
            var player = {};
                player = {id: this.players[i].id, name: this.players[i].name, data: []};
                for(var j in this.players[i].battlefield.cells.data) {
                    player.data.push(this.players[i].battlefield.cells.data[j]);
                }
            arr.push(player);
        }

        console.log(arr);
        console.log(JSON.stringify(arr));

        var self = this,
            ajax = $.ajax({
                //url: "/webservices/PodcastService.asmx/CreateMarkers",
                // The key needs to match your method's input parameter (case-sensitive).
                //data: JSON.stringify({ Markers: markers }),
                contentType: "application/json; charset=utf-8",
                //dataType: "json",
                //contentType: 'application/json',
                dataType: 'json',
                method: 'POST',
                url: self.$area.attr('data-start-link'),
                data: JSON.stringify(arr),
                success: function(response) {
                    console.log('success >>>' , response);
                    $("#debug-area").html(response);
                },
                error: function(response) {
                    console.log('error >>>' , response);
                    $("#debug-area").html(response);
                }
            });
    }
};