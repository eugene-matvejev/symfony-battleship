function Cell(x, y, state) {
    this.x = x;
    this.y = y;
    this.s = state !== undefined ? state : Cell.resources.config.state.seaLive;
    this.$html = Cell.resources.html.layout(this.x, this.y, this.s, undefined);
}

Cell.prototype = {
    setState: function(state) {
        this.s = state;
        this.htmlUpdate(Cell.resources.config.html.attr.state, this.s);

        return this;
    },
    getJSON: function() {
        return {x: this.x, y: this.y};
    },
    htmlUpdate: function(attr, val) {
        this.$html.attr(attr, val);
    }
};

Cell.resources = {};
Cell.resources.config = {
    state: {
        seaLive: 1,
        seaDied: 2,
        shipLive: 3,
        shipDied: 4
    },
    html: {
        attr: {
            x: 'data-x',
            y: 'data-y',
            state: 'data-s'
        }
    }
};
Cell.resources.html = {
    layout: function(x, y, state, txt) {
        var _attr = Cell.resources.config.html.attr;

        return $($.parseHTML(
            '<div class="col-md-1 battlefield-cell"' +
                ' ' + _attr.x + '="' + x + '"' +
                ' ' + _attr.y + '="' + y + '"' +
                ' ' + _attr.state + '="' + state +'">' +
                (txt !== undefined ? txt : '') +
            '</div>'
        ));
    }
};
