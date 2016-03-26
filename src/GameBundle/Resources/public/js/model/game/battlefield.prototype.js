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

            this.cellContainer.xAxisNav.push((new Cell(this.prepareCoordinate(0, y), this)).actAsAxisLabel('digit'));
            this.cellContainer.yAxisNav.push((new Cell(this.prepareCoordinate(y, 0), this)).actAsAxisLabel('letter'));

            this.$html.append($row);

            $row.append(this.cellContainer.xAxisNav[y].$html.clone());
            $top.append(this.cellContainer.yAxisNav[y].$html.clone());

            for (let x = 0; x < this.size; x++) {
                let cell = (new Cell(this.prepareCoordinate(x, y), this)).setState(Cell.resources.config.state.sea.live);
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
     * @param {{id: {int}, coordinate: {string}}} criteria
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
        let ship = Cell.resources.config.state.ship;

        this.findCell({coordinate: "A1"}).setState(ship.dead);
        this.findCell({coordinate: "A2"}).setState(ship.live);
        this.findCell({coordinate: "A3"}).setState(ship.live);

        this.findCell({coordinate: "C3"}).setState(ship.live);
        this.findCell({coordinate: "C4"}).setState(ship.live);
        this.findCell({coordinate: "C5"}).setState(ship.live);

        this.findCell({coordinate: "C1"}).setState(ship.live);
        this.findCell({coordinate: "D1"}).setState(ship.live);
        this.findCell({coordinate: "E1"}).setState(ship.live);
        this.findCell({coordinate: "F1"}).setState(ship.live);

        this.findCell({coordinate: "G5"}).setState(ship.live);
        this.findCell({coordinate: "G6"}).setState(ship.live);

        this.findCell({coordinate: "F3"}).setState(ship.live);
    },
    /**
     * @param {int} x
     * @param {int} y
     *
     * @returns {string}
     */
    prepareCoordinate: function(x, y) {
        return String.fromCharCode(97 + x).toUpperCase() + (1 + y);
    }
};
