$(document).ready(function() {
    var game = new Game();
        game.initNewGame();
        //game.init([{id: 1, name: 'CPU'}, {id: 4, name: 'Player'}]);
        //game.updateHTML();

    $('#game-area')
        .on('click', '.player-area[data-player-typeof="' + Player.typeof.cpu + '"] .battlefield-cell', function(e) {
            e.stopPropagation();
            game.update(this);
        });
    $('.page-sidebar')
        .on('click', 'li[data-action="game-new"]', function(e) {
            e.stopPropagation();
            game.initNewGame();
        });

    $('#modal-area')
        .on('input', '#player-nickname', function(e) {
            e.stopPropagation();
            var mask = /^[a-zA-Z0-9\.\-\ \@]+$/;

            if(this.value.length > 0 && !mask.test(this.value))
                this.value = this.value.substr(0, this.value.length - 1);

            game.modalMgr.isModalFilled();
        })
        .on('input', '#game-battlefield-size', function(e) {
            e.stopPropagation();

            if(this.value.length > 0 && isNaN(this.value))
                this.value = this.value.substr(0, this.value.length - 1);
            else if(this.value.length > 1 && this.value < Game.limits.minBattlefieldSize)
                this.value = Game.limits.minBattlefieldSize;
            else if(this.value.length > 2 || this.value > Game.limits.maxBattlefieldSize)
                this.value = Game.limits.maxBattlefieldSize;

            game.modalMgr.isModalFilled();
        })
        .on('click', '#new-game-btn', function(e) {
            e.stopPropagation();
            game.init(
                [
                    {id:  1, name: 'CPU'},
                    {id: '', name: document.getElementById(Game.indexes.modal.playerName).value}
                ],
                document.getElementById(Game.indexes.modal.battlefieldSize).value
            );
            game.modalMgr.hide();
        });
});