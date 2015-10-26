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

            game.modal.validate(this);
            game.modal.unlockSubmition();
        })
        .on('input', '#game-battlefield-size', function(e) {
            e.stopPropagation();

            game.modal.validate(this);
            game.modal.unlockSubmition();
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