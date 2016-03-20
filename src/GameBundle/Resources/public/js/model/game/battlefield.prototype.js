'use strict';

/**
 * @param {jQuery} $el
 * @param {int}    size
 *
 * @constructor
 */
function Battlefield($el, size) {
    this.$html = $el;
    this.size = size;

    this.init();
}

/**
 * @property {cellContainer} cellContainer
 *
 * @property {jQuery}        $html
 *
 * @property {int}           size
 */
Battlefield.prototype = {
    /**
     * @returns {Battlefield}
     */
    init: function () {
        this.cellContainer = new CellContainer();

        for (let y = 0; y < this.size; y++) {
            this.cellContainer.xAxisNav.push((new Cell(y, 'undefined')).actAsAxisLabel('x'));
            this.cellContainer.yAxisNav.push((new Cell('undefined', y)).actAsAxisLabel('y'));

            for (let x = 0; x < this.size; x++) {
                this.cellContainer.addCell((new Cell(x, y)).setState(Cell.resources.config.state.sea.live));
            }
        }

        let cellLayout = Cell.resources.html.layout,
            $layout = $(CellContainer.resources.html.layout),
            $top = $layout
                .clone()
                .append(cellLayout);

        this.$html.append($top);

        for (let y = 0; y < this.size; y++) {
            let $row = $layout.clone();

            this.$html.append($row);

            $row.append(this.cellContainer.xAxisNav[y].$html.clone());
            $top.append(this.cellContainer.yAxisNav[y].$html.clone());

            for (let x = 0; x < this.size; x++) {
                let _cell = this.getCell(x, y);
                $row.append(_cell.$html);
            }

            $row.append(this.cellContainer.xAxisNav[y].$html.clone());
        }

        $top.append(cellLayout);
        this.$html.append($top.clone());

        return this;
    },
    /**
     * @param {int} x
     * @param {int} y
     *
     * @returns {Cell|undefined}
     */
    getCell: function (x, y) {
        return this.cellContainer.getCell(x, y);
    },
    /**
     * @return {{id: {int|string}}
     */
    getJSON: function () {
        return {id: this.id}
    },
    mockData: function () {
        let shipState = Cell.resources.config.state.ship;

        this.getCell(0, 1).setState(shipState.dead);
        this.getCell(0, 2).setState(shipState.live);
        this.getCell(0, 3).setState(shipState.live);

        this.getCell(2, 2).setState(shipState.live);
        this.getCell(2, 3).setState(shipState.live);

        this.getCell(2, 5).setState(shipState.live);

        this.getCell(2, 0).setState(shipState.live);
        this.getCell(3, 0).setState(shipState.live);
        this.getCell(4, 0).setState(shipState.live);
        this.getCell(5, 0).setState(shipState.live);

        this.getCell(5, 4).setState(shipState.live);
        this.getCell(5, 5).setState(shipState.live);

        this.getCell(4, 2).setState(shipState.live);
    }
};
