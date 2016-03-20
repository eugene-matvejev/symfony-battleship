'use strict';

/**
 * @constructor
 */
function CellContainer() {
    /**
     * @type {Cell[]}
     */
    this.xAxisNav = [];
    /**
     * @type {Cell[]}
     */
    this.yAxisNav = [];
    /**
     * @type {Cell[]}
     */
    this.cells = [];
}

/**
 * @property {Cell[]} xAxisNav
 * @property {Cell[]} yAxisNav
 * @property {Cell[]} cells
 */
CellContainer.prototype = {
    /**
     * @returns {{id: {int}, x: {int}, y: {int}, state: {int}}[]}
     */
    getJSON: function () {
        return this.cells.map(cell => cell.getJSON());
    },
    /**
     * @param {Cell} cell
     *
     * @returns {CellContainer}
     */
    addCell: function (cell) {
        this.cells.push(cell);

        return this;
    },
    /**
     * @param {{id: {int}, x: {int}, y: {int}}} criteria
     *
     * @returns {Cell|undefined}
     */
    findCell: function (criteria) {
        return this.cells.find(function (cell) {
            if (undefined !== criteria.id && cell.id == criteria.id) {
                return cell;
            }
            if (criteria.x !== undefined !== criteria.y && cell.x == criteria.x && cell.y == criteria.y) {
                return cell
            }

            return undefined;
        });
    }
};

CellContainer.resources = {};
CellContainer.resources.html = {
    /** @type {string} */
    layout: '<div class="row battlefield-cell-container"></div>'
};
