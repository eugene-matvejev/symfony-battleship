function Cell(x, y, state) {
    this.x = x;
    this.y = y;
    this.s = state;
    this.$html = Cell.resources.html.layout(this.x, this.y, this.s, undefined);
    this.initModules();
}

Cell.prototype = {
    setState: function(state) {
        this.s = state;
        this.html.update(Cell.resources.config.html.attr.state, this.s);

        return this;
    },
    getJSON: function() {
        return {x: this.x, y: this.y};
    },
    initModules: function() {
        this.html.parent = this;
    },
    html: {
        update: function(attr, val) {
            this.parent.$html.attr(attr, val);
        }
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