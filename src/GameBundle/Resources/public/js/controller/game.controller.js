'use strict';

$(document).ready(function () {
    const FLAG_USERNAME         = 0x01;
    const FLAG_BATTLEFIELD_SIZE = 0x02;
    const FLAG_ALL              = FLAG_USERNAME | FLAG_BATTLEFIELD_SIZE;

    let bytes = 0x00,
        game  = new Game($('div#game-current-area'));

    game.init(
        [
            { name: 'CPU', isCPU: true },
            { name: 'Human' }
        ],
        7
    );

    $('#game-current-area')
        .on('click', '.player-area[data-player-flag="1"] .battlefield-cell[data-state="0"]', function (e) {
            e.stopPropagation();

            game.update(this);
        });
    $('.page-sidebar')
        .on('click', 'li[data-action="game-new-action"]', function (e) {
            e.stopPropagation();

            game.modalGameInitiation();
        });
    $('#modal-area')
        .on('input', '#model-input-player-name', function (e) {
            e.stopPropagation();

            if (!Game.resources.validate.username(this.value)) {
                this.value = this.value.substr(0, this.value.length - 1);
                bytes &= ~FLAG_USERNAME;
            } else {
                bytes |= FLAG_USERNAME;
            }

            game.modalMgr.unlockSubmission((bytes & 0x03) === 0x03);
        })
        .on('input', '#model-input-battlefield-size', function (e) {
            e.stopPropagation();

            let pattern = Game.resources.config.pattern;

            if (!Game.resources.validate.battlefield.size(this.value)) {
                if (isNaN(this.value)) {
                    this.value = this.value.substr(0, this.value.length - 1);
                    bytes &= ~FLAG_BATTLEFIELD_SIZE;
                } else if (this.value > pattern.battlefield.max) {
                    this.value = pattern.battlefield.max;
                    bytes |= FLAG_BATTLEFIELD_SIZE;
                } else {
                    bytes &= ~FLAG_BATTLEFIELD_SIZE;
                }
            } else {
                bytes |= FLAG_BATTLEFIELD_SIZE;
            }

            game.modalMgr.unlockSubmission((bytes & FLAG_ALL) === FLAG_ALL);
        })
        .on('click', '#model-button-init-new-game', function (e) {
            e.stopPropagation();

            game.init(
                [
                    { name: 'CPU', isCPU: true },
                    { name: document.getElementById('model-input-player-name').value }
                ],
                document.getElementById('model-input-battlefield-size').value
            );
        });
});
