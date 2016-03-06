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
    this.id = Math.random();
}

CellContainer.prototype = {
    /**
     * @returns {{x: {int}, y: {int}, s: {int}}[]}
     */
    getJSON: function() {
        return this.cells.map(el => el.getJSON());
    },
    /**
     * @returns {Cell[]}
     */
    getCells: function() {
        return this.cells;
    },
    /**
     * @param {int} x
     * @param {int} y
     *
     * @returns {Cell|undefined}
     */
    getCell: function(x, y) {
        //for(let i in this.cells) {
        //    let cell = this.cells[i];
        //    if(cell.x == x && cell.y == y)
        //        return cell;
        //}
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
