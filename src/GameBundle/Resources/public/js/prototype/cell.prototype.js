'use strict';

class Cell {
    /**
     * @param {number} x
     * @param {number} y
     */
    constructor(x, y) {
        this.$html = $(this.constructor.resources.layout);
        this.setCoordinate(this.constructor.resources.coordinate.factory(x, y));
    }

    /**
     * @param {string} coordinate
     *
     * @returns {Cell}
     */
    setCoordinate(coordinate) {
        this.coordinate = coordinate;
        this.$html.attr('data-coordinate', coordinate);

        return this;
    }

    /**
     * @param {number} id
     *
     * @returns {Cell}
     */
    setId(id) {
        this.id = id;
        this.$html.attr('data-id', id);

        return this;
    }

    /**
     * @param {number} flags
     *
     * @returns {Cell}
     */
    setFlags(flags) {
        this.flags = flags;
        this.$html.attr('data-flags', flags);

        return this;
    }

    /**
     * @param {number} flag
     * 
     * @returns {boolean}
     */
    hasFlag(flag) {
        return (this.flags & flag) === flag;
    }

    /**
     * @param {string} mode
     *
     * @returns {Cell}
     */
    actAsAxisLabel(mode) {
        this.$html.text(this.constructor.resources.coordinate.format[mode](this));

        return this;
    }

    /**
     * @returns {{id: {number}, coordinate: {string}, flags: {number}}}
     */
    getSerializationView() {
        return { id: this.id, coordinate: this.coordinate, flags: this.flags };
    }
}

Cell.resources = {
    /** @enum {number} */
    flags: {
        none: 0x00,
        dead: 0x01,
        ship: 0x02
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
        factory: function (x, y) {
            return String.fromCharCode(97 + x).toUpperCase() + (1 + y);
        },
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
    }
};
