'use strict';

/**
 * @param {string}      coordinate
 * @param {Battlefield} battlefield
 *
 * @constructor
 */
function Cell(coordinate, battlefield) {
    let resources = Cell.resources;

    this.$html = $(resources.html.layout);
    this.battlefield = battlefield;
    this.setId('undefined')
        .setCoordinate(coordinate)
        .setState('undefined');
}

/**
 * @property {jQuery}     $html
 *
 * @property {int|string} id
 * @property {string}     coordinate
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
        this.updateHTML('data-id', this.id);

        return this;
    },
    /**
     * @param {string} coordinate
     *
     * @returns {Cell}
     */
    setCoordinate: function (coordinate) {
        this.coordinate = coordinate;
        this.updateHTML('data-coordinate', this.coordinate);

        return this;
    },
    /**
     * @param {int|string} state
     *
     * @returns {Cell}
     */
    setState: function (state) {
        this.state = state;
        this.updateHTML('data-state', this.state);

        return this;
    },
    /**
     * @param {string} mode
     *
     * @returns {Cell}
     */
    actAsAxisLabel: function (mode) {
        let txt = '',
            format = Cell.resources.text.format;

        switch (mode) {
            case 'letter':
                txt = format.letter(this);
                break;
            case 'digit':
                txt = format.digit(this);
                break;
        }

        this.$html.text(txt);

        return this;
    },
    /**
     * @returns {{id: {int}, coordinate: {string}, state: {int}}}
     */
    getJSON: function () {
        return {id: this.id, coordinate: this.coordinate, state: this.state};
    },
    /**
     * @param {string} attr
     * @param {string} val
     */
    updateHTML: function (attr, val) {
        this.$html.attr(attr, val);
    }
};

Cell.resources = {};
Cell.resources.config = {
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
};
Cell.resources.text = {
    format: {
        /**
         * @param {Cell} cell
         *
         * @returns {string}
         */
        letter: function (cell) {
            return cell.coordinate.charAt(0);
        },
        /**
         * @param {Cell} cell
         *
         * @returns {string}
         */
        digit: function (cell) {
            return cell.coordinate.substring(1);
        }
    }
};
Cell.resources.html = {
    /** @type {string} */
    layout: '<div class="col-md-1 battlefield-cell"></div>'
};
