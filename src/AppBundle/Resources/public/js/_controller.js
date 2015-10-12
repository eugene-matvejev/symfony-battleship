$(document).ready(function() {
    var game = new Game([{id: 1, name: 'CPU'}, {id: 4, name: 'Player'}]);
        game.updateHTML();

    $('#battle-area')
        .on('click', '.battlefield-cell', function(e) {
            e.stopPropagation();
            game.update(this);
        });
});
