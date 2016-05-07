'use strict';

/**
 * this controller server "Game Results" related actions such as pagination and records displaying
 */
$(document).ready(function () {
    let gameResults = new GameResults($('div#game-results-area'));

    $('.page-sidebar')
        .on('click', 'li[data-section="game-results-area"]', function (e) {
            e.stopPropagation();

            gameResults.pageMgr.switchSection(this);
            gameResults.fetch(1);
        });

    $('#game-results-area')
        .on('click', 'button[data-page]', function (e) {
            e.stopPropagation();

            gameResults.fetch(this.getAttribute('data-page'));
        });
});
