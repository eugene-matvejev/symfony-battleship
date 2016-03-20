'use strict';

/**
 * @param {int|string} x
 * @param {int|string} y
 *
 * @constructor
 */
function Cell(x, y) {
    let resources = Cell.resources;

    this.$html = $(resources.html.layout);
    this.setId('undefined')
        .setX(x)
        .setY(y)
        .setState('undefined');
}

/**
 * @property {jQuery}     $html
 *
 * @property {int|string} id
 * @property {int|string} x
 * @property {int|string} y
 * @property {int|string} s
 */
Cell.prototype = {
    /**
     * @param {int|string} id
     *
     * @returns {Cell}
     */
    setId: function (id) {
        this.id = id;
        this.updateHTML('data-cell-id', this.id);

        return this;
    },
    /**
     * @param {int|string} x
     *
     * @returns {Cell}
     */
    setX: function (x) {
        this.x = x;
        this.updateHTML('data-cell-x', this.x);

        return this;
    },
    /**
     * @param {int|string} y
     *
     * @returns {Cell}
     */
    setY: function (y) {
        this.y = y;
        this.updateHTML('data-cell-y', this.y);

        return this;
    },
    /**
     * @param {int|string} state
     *
     * @returns {Cell}
     */
    setState: function (state) {
        this.state = state;
        this.updateHTML('data-cell-state', this.state);

        return this;
    },
    /**
     * @param {string} axis
     *
     * @returns {Cell}
     */
    actAsAxisLabel: function (axis) {
        let txt = '',
            format = Cell.resources.text.format;

        switch (axis) {
            case 'y':
                txt = format.yAxis(this);
                break;
            case 'x':
                txt = format.xAxis(this);
                break;
        }

        this.$html.text(txt);

        return this;
    },
    /**
     * @returns {{id: {int}, x: {int}, y: {int}, state: {int}}}
     */
    getJSON: function () {
        return {id: this.id, x: this.x, y: this.y, state: this.state};
    },
    /**
     * @param {string} attr
     * @param {string} val
     */
    updateHTML: function (attr, val) {
        this.$html.attr(attr, val);
    }
};

Cell.resources = {
    config: {
        state: {
            /** @enum {int} */
            sea: {
                live: 1,
                dead: 2
            },
            /** @enum {int} */
            ship: {
                live: 3,
                dead: 4
            }
        }
        // ,
        // /** @enum {string} */
        // attribute: {
        //     id: 'data-id',
        //     x: 'data-x',
        //     y: 'data-y',
        //     state: 'data-state'
        // }
    },
    text: {
        format: {
            /**
             * @param {Cell} cell
             *
             * @returns {string}
             */
            xAxis: function (cell) {
                return String.fromCharCode(cell.x + 97);
            },
            /**
             * @param {Cell} cell
             *
             * @returns {int}
             */
            yAxis: function (cell) {
                return cell.y + 1;
            }
        }
    },
    html: {
        /** @type {string} */
        layout: '<div class="col-md-1 battlefield-cell"></div>'
    }
};
