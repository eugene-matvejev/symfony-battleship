'use strict';

/**
 * @param {jQuery} $el
 * @param {int}    size
 * @param {Player} player
 *
 * @constructor
 */
function Battlefield($el, size, player) {
    this.$html = $el;
    this.size = size;
    this.player = player;

    this.init();
}

/**
 * @property {jQuery}        $html
 *
 * @property {int}           size
 * @property {Player}        player
 * @property {cellContainer} cellContainer
 */
Battlefield.prototype = {
    /**
     * @returns {Battlefield}
     */
    init: function () {
        this.cellContainer = new CellContainer();

        let cellHTML = Cell.resources.html.layout,
            $cellRow = $(CellContainer.resources.html.layout),
            $top = $cellRow
                .clone()
                .append(cellHTML);

        this.$html.append($top);

        for (let y = 0; y < this.size; y++) {
            let $row = $cellRow.clone();

            this.cellContainer.xAxisNav.push((new Cell(y, 'undefined', this)).actAsAxisLabel('x'));
            this.cellContainer.yAxisNav.push((new Cell('undefined', y, this)).actAsAxisLabel('y'));

            this.$html.append($row);

            $row.append(this.cellContainer.xAxisNav[y].$html.clone());
            $top.append(this.cellContainer.yAxisNav[y].$html.clone());

            for (let x = 0; x < this.size; x++) {
                let cell = (new Cell(x, y, this)).setState(Cell.resources.config.state.sea.live);
                $row.append(cell.$html);
                this.cellContainer.addCell(cell);
            }

            $row.append(this.cellContainer.xAxisNav[y].$html.clone());
        }

        $top.append(cellHTML);
        this.$html.append($top.clone());

        return this;
    },
    /**
     * @param {{id: {int}, x: {int}, y: {int}}} criteria
     *
     * @returns {Cell}
     */
    findCell: function (criteria) {
        return this.cellContainer.findCell(criteria);
    },
    /**
     * @return {{id: {int|string}}
     */
    getJSON: function () {
        return {id: this.id}
    },
    /** *** *** *** *** *** *** *** *** *** *** *** *** *** **/
    mockData: function () {
        let shipState = Cell.resources.config.state.ship;

        this.findCell({x: 0, y: 1}).setState(shipState.dead);
        this.findCell({x: 0, y: 2}).setState(shipState.live);
        this.findCell({x: 0, y: 3}).setState(shipState.live);

        this.findCell({x: 2, y: 2}).setState(shipState.live);
        this.findCell({x: 2, y: 3}).setState(shipState.live);

        this.findCell({x: 2, y: 5}).setState(shipState.live);

        this.findCell({x: 2, y: 0}).setState(shipState.live);
        this.findCell({x: 3, y: 0}).setState(shipState.live);
        this.findCell({x: 4, y: 0}).setState(shipState.live);
        this.findCell({x: 5, y: 0}).setState(shipState.live);

        this.findCell({x: 5, y: 4}).setState(shipState.live);
        this.findCell({x: 5, y: 5}).setState(shipState.live);

        this.findCell({x: 4, y: 2}).setState(shipState.live);
    }
};
