'use strict';

let game = new Game($('div#game-current-area'));

game.init(
    [
        {name: 'CPU', isCPU: true},
        {name: 'Human'}
    ],
    7
);

$('#game-current-area')
    .on('click', '.player-area[data-player-type="1"] .battlefield-cell[data-state="0"]', function (e) {
        e.stopPropagation();

        game.update(this);
    });
$('.page-sidebar')
    .on('click', 'li[data-action="game-new-action"]', function (e) {
        e.stopPropagation();

        game.modalGameInitiation();
    });
$('#modal-area')
    .on('input', '#model-input-player-name, #model-input-battlefield-size', function (e) {
        e.stopPropagation();

        game.modalUnlockSubmission();
    })
    .on('click', '#model-button-init-new-game', function (e) {
        e.stopPropagation();

        game.init(
            [
                {name: 'CPU', isCPU: true},
                {name: document.getElementById('model-input-player-name').value}
            ],
            document.getElementById('model-input-battlefield-size').value
        );
    });
