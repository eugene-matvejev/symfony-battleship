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

Cell.states = {
    seaLive: 1,
    seaDied: 2,
    shipLive: 3,
    shipDied: 4
};
Cell.getHTML = function(x, y, state, txt) {
    return $($.parseHTML('<div class="col-md-1 battlefield-cell" data-x="' + x + '" data-y="' + y + '" data-s="' + state +'">' +
                             (txt !== undefined ? txt : '') +
                         '</div>'));
};