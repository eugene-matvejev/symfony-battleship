'use strict';

/**
 * @param {int|string}        x
 * @param {int|string}        y
 * @param {boolean|undefined} undefinedState
 *
 * @constructor
 */
function Cell(x, y, undefinedState) {
    let resources = Cell.resources;

    this.x = x;
    this.y = y;
    this.s = undefinedState ? 'undefined' : resources.config.state.sea.live;
    this.$html = $(resources.html.layout(this, undefined));
}

/**
 * @property {int|string} x
 * @property {int|string} y
 * @property {int|string} s
 */
Cell.prototype = {
    ///**
    // * @type {int|string}
    // */
    //x: 'undefined',
    ///**
    // * @type {int|string}
    // */
    //y: 'undefined',
    ///**
    // * @type {int|string}
    // */
    //s: 'undefined',
    /**
     * @param {int} state
     *
     * @returns {Cell}
     */
    setState: function(state) {
        this.s = state;
        this.updateHTML(Cell.resources.config.attribute.state, this.s);

        return this;
    },
    /**
     * @returns {{x: {int}, y: {int}, s: {int}}}
     */
    getJSON: function() {
        return {x: this.x, y: this.y, s: this.s};
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
        /**
         * @enum {int}
         */
        sea: {
            live: 1,
            dead: 2
        },
        /**
         * @enum {int}
         */
        ship: {
            live: 3,
            dead: 4
        }
    },
    /**
     * @enum {string}
     */
    attribute: {
        xAxis: 'data-x',
        yAxis: 'data-y',
        state: 'data-s'
    }
};
Cell.resources.html = {
    /**
     * @param {Cell} cell
     * @param {string|undefined} txt
     *
     * @returns {string}
     */
    layout: function(cell, txt) {
        let attribute = Cell.resources.config.attribute;

        return '' +
            '<div class="col-md-1 battlefield-cell" ' +
                 attribute.xAxis + '="' + (cell instanceof Cell ? cell.x : undefined) + '" ' +
                 attribute.yAxis + '="' + (cell instanceof Cell ? cell.y : undefined) + '" ' +
                 attribute.state + '="' + (cell instanceof Cell ? cell.s : undefined) + '">' +
                 (txt || '') +
            '</div>';
    }
};
