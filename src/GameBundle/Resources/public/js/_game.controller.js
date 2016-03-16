'use strict';

$(document).ready(function() {
    let game         = new Game(),
        gameConfig   = Game.resources.config,
        pageConfig   = PageMgr.resources.config,
        playerConfig = Player.resources.config;

    //let el = document.querySelector('li[data-section="' + pageConfig.section.statistics + '"]'),
    //    statisticsMgr = new Statistics();
    //    statisticsMgr.pageMgr.switchSection(el);
    //    statisticsMgr.fetch(1);

    game.pageMgr.switchSection(document.querySelector('li[data-action="' + pageConfig.action.game.new + '"]'));
    game.init(
        [
            {name: 'CPU', isCPU: true},
            {name: 'Human'}
        ],
        7
    );

    $('#game-area')
        .on('click', '.player-area:not([' + playerConfig.attribute.type + '="' + playerConfig.type.player + '"]):not(.finished)' +
                    ' .battlefield-cell[data-s="' + Cell.resources.config.state.sea.live + '"]', function(e) {
            e.stopPropagation();

            game.update(this);
        });
    $('.page-sidebar')
        .on('click', 'li[data-action="' + pageConfig.action.game.new + '"]', function(e) {
            e.stopPropagation();

            game.modalGameInitiation();
        });
    $('#modal-area')
        .on('input', '#' + gameConfig.trigger.player + ', #' + gameConfig.trigger.bfsize, function(e) {
            e.stopPropagation();

            game.modalUnlockSubmission();
        })
        .on('click', '#new-game-btn', function(e) {
            e.stopPropagation();

            game.init(
                [
                    {name: 'CPU', isCPU: true},
                    {name: document.getElementById(gameConfig.trigger.player).value}
                ],
                document.getElementById(gameConfig.trigger.bfsize).value
            );
        });
});
