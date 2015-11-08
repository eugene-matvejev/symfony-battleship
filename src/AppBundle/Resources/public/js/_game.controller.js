$(document).ready(function() {
    var game         = new Game(),
        gameConfig   = Game.resources.config,
        cellConfig   = Cell.resources.config,
        pageConfig   = PageMgr.resources.config,
        playerConfig = Player.resources.config;
    game.pageMgr.switchSection(document.querySelector('li[data-action="' + pageConfig.action.game.new + '"]'));
    console.log('.player-area:not(.finished)[' + playerConfig.trigger.type +'="' + playerConfig.type.cpu + '"] .battlefield-cell[data-s="' + cellConfig.state.seaLive + '"]');
    //game.modal.initGame();
    game.init(
        [
            {id:  1, name: 'CPU'},
            {id: '', name: 'Human'}
        ],
        7
    );

    $('#game-area')
        .on('click', '.player-area:not(.finished)[' + playerConfig.trigger.type +'="' + playerConfig.type.cpu + '"] .battlefield-cell[data-s="' + cellConfig.state.seaLive + '"]', function(e) {
            debugger;
            e.stopPropagation();

            game.update(this);
        });
    $('.page-sidebar')
        .on('click', 'li[data-action="' + pageConfig.action.game.new + '"]', function(e) {
            e.stopPropagation();

            game.modal.initGame();
        });

    $('#modal-area')
        .on('input', '#' + gameConfig.trigger.player + ', #' + gameConfig.trigger.bfsize, function(e) {
            e.stopPropagation();

            game.modal.unlockSubmition();
        })
        .on('click', '#new-game-btn', function(e) {
            e.stopPropagation();

            //game.modalMgr.hide();
            game.init(
                [
                    {id:  1, name: 'CPU'},
                    {id: '', name: document.getElementById(gameConfig.trigger.player).value}
                ],
                document.getElementById(gameConfig.trigger.bfsize).value
            );
        });
});
