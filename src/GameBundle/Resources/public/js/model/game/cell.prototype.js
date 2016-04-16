'use strict';

class Cell {
    /**
     * @param {string}      coordinate
     * @param {Battlefield} battlefield
     */
    constructor(coordinate, battlefield) {
        this.battlefield = battlefield;
        this.$html       = $(this.constructor.resources.layout);
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
        this.$html.attr('data-coordinate', this.coordinate);

        return this;
    }

    /**
     * @param {int|string} id
     *
     * @returns {Cell}
     */
    setId(id) {
        this.id = id;
        this.$html.attr('data-id', this.id);

        return this;
    }

    /**
     * @param {int|string} state
     *
     * @returns {Cell}
     */
    setState(state) {
        this.state = state;
        this.$html.attr('data-state', this.state);

        return this;
    }

    /**
     * @param {string} mode
     *
     * @returns {Cell}
     */
    actAsAxisLabel(mode) {
        let coordinate = this.constructor.resources.coordinate;

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
     * @returns {{id: {int}, coordinate: {string}, state: {int}}}
     */
    getJSON() {
        return { id: this.id, coordinate: this.coordinate, state: this.state };
    }
}

Cell.resources = {
    /** @enum {int} */
    mask: {
        none: 0x0000,
        dead: 0x0001,
        ship: 0x0002,
        skip: 0x0004,
        deadShip: 0x0002|0x0001
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
};
