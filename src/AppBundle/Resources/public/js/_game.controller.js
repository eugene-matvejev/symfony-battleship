$(document).ready(function() {
    var game = new Game();
        game.pageMgr.switchSection(document.querySelector('li[data-action="' + PageMgr.resource.config.action.game.new + '"]'));
    //game.modal.initGame();
    game.init(
        [
            {id:  1, name: 'CPU'},
            {id: '', name: 'Human'}
        ],
        7
    );

    $('#game-area')
        .on('click', '.player-area:not(.finished)[' + Player.resource.config.trigger.type +'="' + Player.resource.config.type.cpu + '"] .battlefield-cell[data-s="' + Cell.state.seaLive + '"]', function(e) {
            e.stopPropagation();

            game.update(this);
        });
    $('.page-sidebar')
        .on('click', 'li[data-action="' + PageMgr.resource.config.action.game.new + '"]', function(e) {
            e.stopPropagation();

            game.modal.initGame();
        });

    $('#modal-area')
        .on('input', '#' + Game.resource.config.trigger.player + ', #' + Game.resource.config.trigger.bfsize, function(e) {
            e.stopPropagation();

            game.modal.unlockSubmition();
        })
        .on('click', '#new-game-btn', function(e) {
            e.stopPropagation();

            //game.modalMgr.hide();
            game.init(
                [
                    {id:  1, name: 'CPU'},
                    {id: '', name: document.getElementById(Game.resource.config.trigger.player).value}
                ],
                document.getElementById(Game.resource.config.trigger.bfsize).value
            );
        });
});
