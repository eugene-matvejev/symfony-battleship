'use strict';

/**
 * @param {int} x
 * @param {int} y
 * @param {int|undefined} state
 *
 * @constructor
 */
function Cell(x, y, state) {
    let resources = Cell.resources;

    this.x = x;
    this.y = y;
    this.s = undefined !== state ? state : resources.config.state.sea.live;
    this.$html = $(resources.html.layout(this.x, this.y, this.s, undefined));
}

Cell.prototype = {
    /**
     * @type {int}
     */
    x: 'undefined',
    /**
     * @type {int}
     */
    y: 'undefined',
    /**
     * @type {int|undefined}
     */
    s: 'undefined',
    /**
     * @param {int} state
     *
     * @returns {Cell}
     */
    setState: function(state) {
        this.s = state;
        this.updateHTML(Cell.resources.config.html.attr.state, this.s);

        return this;
    },
    /**
     * @returns {{x: {int}, y: {int}}}
     */
    getJSON: function() {
        return {x: this.x, y: this.y};
    },
    /**
     * @param {string}     attr
     * @param {string|int} val
     *
     * @returns {void}
     */
    updateHTML: function(attr, val) {
        this.$html.attr(attr, val);
    }
};

Cell.resources = {};
Cell.resources.config = {
    state: {
        sea: {
            /**
             * @type {int}
             */
            live: 1,
            /**
             * @type {int}
             */
            dead: 2
        },
        ship: {
            /**
             * @type {int}
             */
            live: 3,
            /**
             * @type {int}
             */
            dead: 4
        }
    },
    attribute: {
        /**
         * @type {string}
         */
        xAxis: 'data-x',
        /**
         * @type {string}
         */
        yAxis: 'data-y',
        /**
         * @type {string}
         */
        state: 'data-s'
    }
};
Cell.resources.html = {
    /**
     * @param {int} x
     * @param {int} y
     * @param {int} state
     * @param {string|undefined} txt
     *
     * @returns {string}
     */
    layout: function(x, y, state, txt) {
        let attribute = Cell.resources.config.attribute;

        return '' +
            '<div class="col-md-1 battlefield-cell" ' +
                 attribute.xAxis + '="' + x + '" ' +
                 attribute.yAxis + '="' + y + '" ' +
                 attribute.state + '="' + state +'">' +
                (undefined !== txt ? txt : '') +
            '</div>';
    }
};
