'use strict';

class Battlefield extends CellContainer {
    /**
     * @param {jQuery} $el
     * @param {number} size
     * @param {Player} player
     */
    constructor($el, size, player) {
        super();

        this.$html  = $el;
        this.size   = size;
        this.player = player;

        this.init();
    }

    /**
     * @returns {Battlefield}
     */
    init() {
        let layout     = Cell.resources.layout,
            factory    = Cell.resources.coordinate,
            $container = $(super.constructor.resources.layout),
            /** append < first , first > transparent cell to top decoration row */
            $top       = $container.clone().append(layout);

        this.$html.append($top);

        for (let y = 0; y < this.size; y++) {
            let $row = $container.clone();

            this.xAxis.push((new Cell(factory.raw(0, y), this)).actAsAxisLabel('digit'));
            this.yAxis.push((new Cell(factory.raw(y, 0), this)).actAsAxisLabel('letter'));

            this.$html.append($row);

            $row.append(this.xAxis[y].$html.clone());
            $top.append(this.yAxis[y].$html.clone());

            for (let x = 0; x < this.size; x++) {
                let cell = (new Cell(factory.raw(x, y), this)).setState(0x0000);
                $row.append(cell.$html);
                this.addCell(cell);
            }

            $row.append(this.xAxis[y].$html.clone());
        }

        /** append < last , last > transparent cell to top decoration row */
        $top.append(layout);
        /** duplicate top navigation row at bottom */
        this.$html.append($top.clone());

        return this;
    }

    /** *** *** *** *** *** *** *** DATA MOCK *** *** *** *** *** *** **/
    initPlayerCells() {
        ['A1', 'A2', 'A3', 'C3', 'C4', 'C5', 'C1', 'D1', 'E1', 'F1', 'G5', 'G6', 'F3'].forEach(
            coordinate => this.findByCriteria({ coordinate: coordinate }).setState(Cell.resources.mask.ship),
            this
        );
    }
}
