'use strict';

class Cell {
    /**
     * @param {string}      coordinate
     * @param {Battlefield} battlefield
     */
    constructor(coordinate, battlefield) {
        this.battlefield = battlefield;
        this.$html       = $(Cell.resources.layout);
        this.setId('undefined')
            .setCoordinate(coordinate)
            .setState('undefined');
    }

    /**
     * @param {string} coordinate
     *
     * @returns {Cell}
     */
    setCoordinate(coordinate) {
        this.coordinate = coordinate;
        this.updateHTML('data-coordinate', this.coordinate);

        return this;
    }

    /**
     * @param {int|string} id
     *
     * @returns {Cell}
     */
    setId(id) {
        this.id = id;
        this.updateHTML('data-id', this.id);

        return this;
    }

    /**
     * @param {int|string} state
     *
     * @returns {Cell}
     */
    setState(state) {
        this.state = state;
        this.updateHTML('data-state', this.state);

        return this;
    }

    /**
     * @param {string} mode
     *
     * @returns {Cell}
     */
    actAsAxisLabel(mode) {
        let coordinate = Cell.resources.coordinate;

        switch (mode) {
            case 'letter':
                this.$html.text(coordinate.letterOnly(this));
                break;
            case 'digit':
                this.$html.text(coordinate.digitOnly(this));
                break;
        }

        return this;
    }

    /**
     * @param {string} attr
     * @param {string} val
     */
    updateHTML(attr, val) {
        this.$html.attr(attr, val);
    }

    /**
     * @returns {{id: {int}, coordinate: {string}, state: {int}}}
     */
    getJSON() {
        return { id: this.id, coordinate: this.coordinate, state: this.state };
    }
}

Cell.resources = {
    /** @enum {int} */
    state: {
        waterLive: 1,
        waterDead: 2,
        waterSkip: 5,
        shipLive: 3,
        shipDead: 4
    },
    /**
     * @type {string}
     */
    layout: '<div class="col-md-1 battlefield-cell"></div>',
    coordinate: {
        /**
         * @param {int} x
         * @param {int} y
         *
         * @returns {string}
         */
        full: function (x, y) {
            return String.fromCharCode(97 + x).toUpperCase() + (1 + y);
        },
        /**
         * @param {Cell} cell
         *
         * @returns {string}
         */
        letterOnly: function (cell) {
            return cell.coordinate.charAt(0);
        },
        /**
         * @param {Cell} cell
         *
         * @returns {string}
         */
        digitOnly: function (cell) {
            return cell.coordinate.substring(1);
        }

    }
}
