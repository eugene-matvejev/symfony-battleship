function Cell(x, y, state) {
    this.x = x;
    this.y = y;
    this.s = state;
}

Cell.prototype = {
    setState: function(state) {
        this.s = state;

        return this;
    },
    getHTML: function() {
        return Cell.getHTML(this.x, this.y, this.s);
    }
};

Cell.tag    = { x: 'data-x', y: 'data-y', state: 'data-s' };
Cell.states = { seaLive: 1, seaDied: 2, shipLive: 3, shipDied: 4 };
Cell.getHTML = function(x, y, state, txt) {
    return $($.parseHTML('<div class="col-md-1 battlefield-cell"' +
                    ' ' + Cell.tag.x + '="' + x + '"' +
                    ' ' + Cell.tag.y + '="' + y + '"' +
                    ' ' + Cell.tag.state + '="' + state +'">' +
                             (txt !== undefined ? txt : '') +
                         '</div>'));
};
