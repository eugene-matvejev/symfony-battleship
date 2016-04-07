'use strict';

class Battlefield {
    /**
     * @param {jQuery} $el
     * @param {int}    size
     * @param {Player} player
     */
    constructor($el, size, player) {
        this.$html  = $el;
        this.size   = size;
        this.player = player;

        this.init();
    }

    /**
     * @returns {Battlefield}
     */
    init() {
        this.cellContainer = new CellContainer();

        let cellLayout     = Cell.resources.layout,
            cellCoordinate = Cell.resources.coordinate,
            $cellContainer = $(CellContainer.resources.layout),
            $top           = $cellContainer
                .clone()
                .append(cellLayout);

        this.$html.append($top);

        for (let y = 0; y < this.size; y++) {
            let $row = $cellContainer.clone();

            this.cellContainer.xAxisNav.push((new Cell(cellCoordinate.full(0, y), this)).actAsAxisLabel('digit'));
            this.cellContainer.yAxisNav.push((new Cell(cellCoordinate.full(y, 0), this)).actAsAxisLabel('letter'));

            this.$html.append($row);

            $row.append(this.cellContainer.xAxisNav[y].$html.clone());
            $top.append(this.cellContainer.yAxisNav[y].$html.clone());

            for (let x = 0; x < this.size; x++) {
                let cell = (new Cell(cellCoordinate.full(x, y), this)).setState(Cell.resources.state.waterLive);
                $row.append(cell.$html);
                this.cellContainer.addCell(cell);
            }

            $row.append(this.cellContainer.xAxisNav[y].$html.clone());
        }

        $top.append(cellLayout);
        this.$html.append($top.clone());

        return this;
    }

    /**
     * @param {{id: {int}, coordinate: {string}}} criteria
     *
     * @returns {Cell}
     */
    findCell(criteria) {
        return this.cellContainer.findCell(criteria);
    }

    /**
     * @return {{id: {int|string}}
     */
    getJSON() {
        return { id: this.id }
    }

    /** *** *** *** *** *** *** *** *** *** *** *** *** *** **/
    mockData() {
        let self  = this,
            state = Cell.resources.state;

        ["A1"].forEach(function (coordinate) {
            self.findCell({ coordinate: coordinate }).setState(state.shipDead);
        });
        ["A2", "A3", "C3", "C4", "C5", "C1", "D1", "E1", "F1", "G5", "G6", "F3"].forEach(function (coordinate) {
            self.findCell({ coordinate: coordinate }).setState(state.shipLive);
        });
    }
}
