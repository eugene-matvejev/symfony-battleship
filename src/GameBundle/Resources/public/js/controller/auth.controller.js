'use strict';

/**
 * this controller serve Authorization
 * @see Auth
 */
$(document).ready(function () {
    let auth = new Auth();

    $('#modal-area')
        .on('input', '#model-input-password', function (e) {

        })
        .on('click', '#button-form-submit', function (e) {
            e.stopPropagation();

            let username = $('#model-input-username').value();
            let password = $('#model-input-password').value();
        });
    //     /** modal area: player name */
    //     e.stopPropagation();
    //
    //     Game.resources.validate.username(this.value)
    //         ? bytes |= FLAG_USERNAME
    //         : bytes &= ~FLAG_USERNAME;
    //
    //     highlightInputSection(this.parentElement, FLAG_USERNAME);
    // })
    // .on('input', '#model-input-battlefield-size', function (e) {
    //     /** modal area: battlefield size */
    //     e.stopPropagation();
    //
    //     let pattern = Game.resources.config.pattern;
    //
    //     if (!isNaN(this.value) && this.value > pattern.battlefield.max) {
    //         this.value = pattern.battlefield.max;
    //     }
    //
    //     Game.resources.validate.battlefield.size(this.value)
    //         ? bytes |= FLAG_BATTLEFIELD_SIZE
    //         : bytes &= ~FLAG_BATTLEFIELD_SIZE;
    //
    //     highlightInputSection(this.parentElement, FLAG_BATTLEFIELD_SIZE);
    // })
    // .on('click', '#model-button-init-new-game', function (e) {
    //     /** modal area: submit */
    //     e.stopPropagation();
    //
    //     game.pageMgr.switchSection(document.querySelector('.page-sidebar li[data-section="game-current-area"]'));
    //     game.init(
    //         document.getElementById('model-input-player-name').value,
    //         document.getElementById('model-input-battlefield-size').value
    //     );
});
