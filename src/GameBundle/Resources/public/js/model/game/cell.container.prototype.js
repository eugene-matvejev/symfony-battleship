'use strict';

class CellContainer {
    constructor() {
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
     * @param {Cell} cell
     *
     * @returns {CellContainer}
     */
    addCell(cell) {
        this.cells.push(cell);

        return this;
    }

    /**
     * @param {{id: {number}, coordinate: {string}}} obj
     *
     * @returns {Cell|undefined}
     */
    findCellByCriteria(obj) {
        return this.cells.find(cell => (undefined !== obj.id && cell.id == obj.id) || (undefined !== obj.coordinate && cell.coordinate === obj.coordinate));
    }

    /**
     * @returns {{id: {number}, x: {number}, y: {number}, state: {number}}[]}
     */
    getJSON() {
        return this.cells.map(cell => cell.getJSON());
    }
}

CellContainer.resources = {
    /** @type {string} */
    layout: '<div class="row battlefield-cell-container"></div>'
};
