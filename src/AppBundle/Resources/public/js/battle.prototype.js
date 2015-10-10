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
    },
    update: function(el) {
        //var player = $el.parent().parent().attr('data-player-id'),
        //    x      = $el.attr('data-x'),
        //    y      = $el.attr('data-y'),
        //    state  = $el.attr('data-s');
        var player = el.parentElement.parentElement.getAttribute('data-player-id'),
            x      = el.getAttribute('data-x'),
            y      = el.getAttribute('data-y'),
            state  = el.getAttribute('data-s');

        console.log(player, x, y, state);
        var cell = this.getCellData(player, x, y);
        console.log(cell);
    },
    getCellData: function(playerId, x, y) {
        return this.getPlayer(playerId).battlefield.getCellData(x, y);
    },
    getPlayer: function(playerId) {
        for(var i in this.players) {
            if(this.players[i].id == playerId)
                return this.players[i];
        }
        return undefined;
    },
    sendCell: function(action, cell) {
        var self = this;
        $.ajax({
            url: '/action',
            method: 'GET',
            data: cell,
            success: function(response) {
                console.log('success >>>' , response);
            },
            error: function(response) {
                console.log('error >>>' , response);
            }
        })
    }
};