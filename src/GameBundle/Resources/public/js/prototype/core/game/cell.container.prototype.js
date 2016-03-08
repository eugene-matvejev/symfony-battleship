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
     * @returns {{x: {int}, y: {int}, s: {int}}[]}
     */
    getJSON: function() {
        return this.cells.map(el => el.getJSON());
    },
    /**
     * @param {int} x
     * @param {int} y
     *
     * @returns {Cell|undefined}
     */
    getCell: function(x, y) {
        return this.cells.find(el => el.x === x && el.y === y);
    },
    /**
     * @param {Cell} cell
     *
     * @returns {CellContainer}
     */
    addCell: function(cell) {
        this.cells.push(cell);

        return this;
    }
};

CellContainer.resources = {};
CellContainer.resources.html = {
    /**
     * @type {string}
     */
    layout: '<div class="row battlefield-cell-container"></div>'
};
