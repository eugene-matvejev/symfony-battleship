'use strict';

class Cell {
    /**
     * @param {string}      coordinate
     * @param {Battlefield} battlefield
     */
    constructor(coordinate, battlefield) {
        this.battlefield = battlefield;
        this.$html       = $(this.constructor.resources.layout);
        this.setCoordinate(coordinate);
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
     * @param {number} id
     *
     * @returns {Cell}
     */
    setId(id) {
        this.id = id;
        this.$html.attr('data-id', this.id);

        return this;
    }

    /**
     * @param {number} state
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
        let format = this.constructor.resources.coordinate.format;

        switch (mode) {
            case 'letter':
                this.$html.text(format.letterOnly(this));
                break;
            case 'digit':
                this.$html.text(format.digitOnly(this));
                break;
        }

        return this;
    }

    /**
     * @returns {{id: {number}, coordinate: {string}, flags: {number}}}
     */
    getSerializationView() {
        return { id: this.id, coordinate: this.coordinate, flags: this.state };
    }
}

Cell.resources = {
    /** @enum {number} */
    mask: {
        dead: 0x0001,
        ship: 0x0002,
        deadShip: 0x0002 | 0x0001,
        skip: 0x0004 | 0x0001
    },
    /** @type {string} */
    layout: '<div class="col-md-1 battlefield-cell"></div>',
    coordinate: {
        /**
         * @param {number} x
         * @param {number} y
         *
         * @returns {string}
         */
        raw: function (x, y) {
            return String.fromCharCode(97 + x).toUpperCase() + (1 + y);
        },
        format: {
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
};
