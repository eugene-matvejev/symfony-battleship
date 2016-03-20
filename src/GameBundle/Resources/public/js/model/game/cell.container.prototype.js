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
     * @param {int} x
     * @param {int} y
     *
     * @returns {Cell|undefined}
     */
    getCell: function (x, y) {
        return this.cells.find(cell => cell.x === x && cell.y === y);
    }
};

CellContainer.resources = {};
CellContainer.resources.html = {
    /** @type {string} */
    layout: '<div class="row battlefield-cell-container"></div>'
};
