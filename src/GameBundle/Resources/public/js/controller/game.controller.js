'use strict';

$(document).ready(function () {
    let game = new Game($('div#game-current-area'));

    game.init(
        [
            {name: 'CPU', isCPU: true},
            {name: 'Human'}
        ],
        7
    );

    $('#game-current-area')
        .on('click', '.player-area[data-player-type="1"] .battlefield-cell[data-state="1"]', function (e) {
            e.stopPropagation();

            game.update(this);
        });
    $('.page-sidebar')
        .on('click', 'li[data-action="game-new-action"]', function (e) {
            e.stopPropagation();

            game.modalGameInitiation();
        });
    $('#modal-area')
        .on('input', '#model-trigger-username, #model-trigger-battlefield-size', function (e) {
            e.stopPropagation();

            game.modalUnlockSubmission();
        })
        .on('click', '#new-game-btn', function (e) {
            e.stopPropagation();

            game.init(
                [
                    {name: 'CPU', isCPU: true},
                    {name: document.getElementById('model-trigger-username').value}
                ],
                document.getElementById('model-trigger-battlefield-size').value
            );
        });
});
