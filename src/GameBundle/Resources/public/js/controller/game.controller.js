'use strict';

/**
 * this controller serve all "Game" related actions such as game initiation
 */
$(document).ready(function () {
    const FLAG_NONE             = 0x00;
    const FLAG_USERNAME         = 0x01;
    const FLAG_BATTLEFIELD_SIZE = 0x02;
    const FLAG_ALL              = FLAG_USERNAME | FLAG_BATTLEFIELD_SIZE;

    let bytes              = FLAG_NONE,
        game               = new Game($('div#game-current-area')),
        highlightFormGroup = function (el, flag) {
            let $el = $(el.parentElement);
            $el.removeClass('has-success has-error').addClass((bytes & flag) === flag ? 'has-success' : 'has-error');
        };

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
            /** modal area: player name */
            e.stopPropagation();

            if (!Game.resources.validate.username(this.value)) {
                bytes &= ~FLAG_USERNAME;
            } else {
                bytes |= FLAG_USERNAME;
            }

            highlightFormGroup(this, FLAG_USERNAME);
            game.modalMgr.unlockSubmission((bytes & FLAG_ALL) === FLAG_ALL);
        })
        .on('input', '#model-input-battlefield-size', function (e) {
            /** modal area: battlefield size */
            e.stopPropagation();

            let pattern = Game.resources.config.pattern;

            if (!Game.resources.validate.battlefield.size(this.value)) {
                if (isNaN(this.value)) {
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

            highlightFormGroup(this, FLAG_BATTLEFIELD_SIZE);
            game.modalMgr.unlockSubmission((bytes & FLAG_ALL) === FLAG_ALL);
        })
        .on('click', '#model-button-init-new-game', function (e) {
            /** modal area: submit */
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
