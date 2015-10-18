$(document).ready(function() {
    var game = new Game([{id: 1, name: 'CPU'}, {id: 4, name: 'Player'}]);
        game.updateHTML();

    $('#game-area')
        .on('click', '.player-area[data-player-typeof="' + Player.typeof.cpu + '"] .battlefield-cell', function (e) {
            e.stopPropagation();
            game.update(this);
        });
});