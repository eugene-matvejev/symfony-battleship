'use strict';

class Battlefield {
    /**
     * @param {jQuery} $el
     * @param {number} size
     */
    constructor($el, size) {
        this.$html = $el;
        this.cells = [];

        this.init(size);
    }

    /**
     * @param {number} size
     *
     * @returns {Battlefield}
     */
    init(size) {
        let layout     = Cell.resources.layout,
            $container = $(this.constructor.resources.cellRowLayout),
            /** append < first , first > transparent cell to top decoration row */
            $top       = $container.clone().append(layout);

        this.$html.append($top);

        for (let y = 0; y < size; y++) {
            let $row = $container.clone();

            this.$html.append($row);

            $row.append((new Cell(0, y)).actAsAxisLabel('digit').$html);
            $top.append((new Cell(y, 0)).actAsAxisLabel('letter').$html);

            for (let x = 0; x < size; x++) {
                let cell = (new Cell(x, y)).setFlags(Cell.resources.flags.none);
                this.addCell(cell);
                $row.append(cell.$html);
            }

            $row.append($row.find(':first-child').clone());
        }

        /** append < last , last > transparent cell to top decoration row */
        $top.append(layout);
        /** duplicate top navigation row at bottom */
        this.$html.append($top.clone());

        return this;
    }

    /**
     * @param {Cell} cell
     *
     * @returns {Battlefield}
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
    findCellByCriteria(criteria) {
        return this.cells.find(cell =>
            (undefined !== criteria.id && cell.id === criteria.id) ||
            (undefined !== criteria.coordinate && cell.coordinate === criteria.coordinate)
        );
    }

    /** *** *** *** *** *** *** *** DATA MOCK *** *** *** *** *** *** **/
    initPlayerCells() {
        ['A1', 'A2', 'A3', 'C3', 'C4', 'C5', 'C1', 'D1', 'E1', 'F1', 'G5', 'G6', 'F3'].forEach(
            coordinate => this.findCellByCriteria({ coordinate: coordinate }).setFlags(Cell.resources.flags.ship),
            this
        );
    }
}

Battlefield.resources = {
    /** @type {string} */
    cellRowLayout: '<div class="row battlefield-cell-container"></div>'
};
