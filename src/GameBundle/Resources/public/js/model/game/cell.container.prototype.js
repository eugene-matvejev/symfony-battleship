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
     * @param {{id: {int}, coordinate: {string}}} criteria
     *
     * @returns {Cell|undefined}
     */
    findCell(criteria) {
        return this.cells.find(function (cell) {
            if (undefined !== criteria.id && cell.id == criteria.id) {
                return cell;
            }
            if (undefined !== criteria.coordinate && cell.coordinate == criteria.coordinate) {
                return cell
            }

            return undefined;
        });
    }

    /**
     * @returns {{id: {int}, x: {int}, y: {int}, state: {int}}[]}
     */
    getJSON() {
        return this.cells.map(cell => cell.getJSON());
    }
}

CellContainer.resources = {
    /** @type {string} */
    layout: '<div class="row battlefield-cell-container"></div>'
};
