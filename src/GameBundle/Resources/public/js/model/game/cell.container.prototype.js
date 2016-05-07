'use strict';

class CellContainer {
    constructor() {
        /** @type {Cell[]} for decoration purposes only */
        this.xAxis = [];
        /** @type {Cell[]} for decoration purposes only */
        this.yAxis = [];
        /** @type {Cell[]} game cells */
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
     * @param {{id: {number}, coordinate: {string}}} criteria
     *
     * @returns {?Cell}
     */
    findByCriteria(criteria) {
        return this.cells.find(cell =>
            (undefined !== criteria.id && cell.id === criteria.id) ||
            (undefined !== criteria.coordinate && cell.coordinate === criteria.coordinate)
        );
    }
}

CellContainer.resources = {
    /** @type {string} */
    layout: '<div class="row battlefield-cell-container"></div>'
};
