$(document).ready(function() {
    var game = new Game();
        game.pageMgr.switchSection(document.querySelector('li[data-action="game-new"]'));
        game.modal.initGame();

    $('#game-area')
        .on('click', '.player-area:not(.finished)[data-player-typeof="' + Player.typeof.cpu + '"] .battlefield-cell[data-s="' + Cell.state.seaLive + '"]', function(e) {
            e.stopPropagation();

            game.update(this);
        });
    $('.page-sidebar')
        .on('click', 'li[data-action="game-new"]', function(e) {
            e.stopPropagation();

            game.modal.initGame();
        });

    $('#modal-area')
        .on('input', '#' + Game.resource.config.trigger.player + ', #' + Game.resource.config.trigger.bfsize, function(e) {
            e.stopPropagation();

            game.modal.unlockSubmition();
        })
        //.on('input', '#player-nickname', function(e) {
        //    e.stopPropagation();
        //
        //    game.modal.validate(this);
        //    game.modal.unlockSubmition();
        //})
        //.on('input', '#game-battlefield-size', function(e) {
        //    e.stopPropagation();
        //
        //    game.modal.validate(this);
        //    game.modal.unlockSubmition();
        //})
        .on('click', '#new-game-btn', function(e) {
            e.stopPropagation();

            game.modalMgr.hide();
            game.init(
                [
                    {id:  1, name: 'CPU'},
                    {id: '', name: document.getElementById(Game.indexes.modal.playerName).value}
                ],
                document.getElementById(Game.indexes.modal.battlefieldSize).value
            );
        });
});
