'use strict';

/**
 * @param {int}    size
 * @param {jQuery} $el
 *
 * @constructor
 */
function Battlefield(size, $el) {
    this.$html = $el;
    this.size  = size;
    //this.cellContainer = new CellContainer();
    this.init()
        .updateHTML();
}

Battlefield.prototype = {
    ///**
    // * @type {int|string}
    // */
    //id: 'undefined',
    ///**
    // * @type {int}
    // */
    //size: 'undefined',
    ///**
    // * @type {CellContainer}
    // */
    //cellContainer: undefined,
    /**
     * @returns {Battlefield}
     */
    init: function() {
        this.cellContainer = new CellContainer();

        for(let y = 0; y < this.size; y++) {
            /** parseInt(y) because of JSDoc */
            this.cellContainer.xAxisNav.push(new Cell(parseInt(y), 'undefined', true));
            this.cellContainer.yAxisNav.push(new Cell('undefined', parseInt(y), true));

            for(let x = 0; x < this.size; x++) {
                this.cellContainer.addCell(new Cell(x, y, false));
            }
        }

        return this;
    },
    updateHTML: function() {
        let cellHTML = Cell.resources.html.layout,
            $layout  = $(CellContainer.resources.html.layout),
            $top     = $layout
                        .clone()
                        .append(cellHTML()),
            format   = Battlefield.resources.config.format;

        this.$html.append($top);

        for(let y = 0; y < this.size; y++) {
            let $row = $layout.clone();
            this.$html.append($row);

            $row.append(cellHTML(this.cellContainer.yAxisNav[y], format.yAxis(this.cellContainer.yAxisNav[y])));
            $top.append(cellHTML(this.cellContainer.xAxisNav[y], format.xAxis(this.cellContainer.xAxisNav[y])));

            for(let x = 0; x < this.size; x++) {
                $row.append(this.getCell(x, y).$html);
            }

            $row.append(cellHTML(this.cellContainer.yAxisNav[y], format.yAxis(this.cellContainer.yAxisNav[y])));
        }

        $top.append(cellHTML());
        this.$html.append($top.clone());
    },
    /**
     * @param {int} x
     * @param {int} y
     *
     * @returns {Cell|undefined}
     */
    getCell: function(x, y) {
        return this.cellContainer.getCell(x, y);
    },
    /**
     * @return {{id: {int|string}}
     */
    getJSON: function() {
        return {id: this.id}
    },
    mockData: function() {
        let state = Cell.resources.config.state;

        this.getCell(0, 1).setState(state.ship.dead);
        this.getCell(0, 2).setState(state.ship.live);
        this.getCell(0, 3).setState(state.ship.live);

        this.getCell(2, 2).setState(state.ship.live);
        this.getCell(2, 3).setState(state.ship.live);

        this.getCell(2, 5).setState(state.ship.live);

        this.getCell(2, 0).setState(state.ship.live);
        this.getCell(3, 0).setState(state.ship.live);
        this.getCell(4, 0).setState(state.ship.live);
        this.getCell(5, 0).setState(state.ship.live);

        this.getCell(5, 4).setState(state.ship.live);
        this.getCell(5, 5).setState(state.ship.live);

        this.getCell(4, 2).setState(state.ship.live);
    }
};

Battlefield.resources = {
    config: {
        format: {
            /**
             * @param {Cell} cell
             *
             * @returns {string}
             */
            xAxis: function(cell) {
                return String.fromCharCode(cell.x + 97);
            },
            /**
             * @param {Cell} cell
             *
             * @returns {int}
             */
            yAxis: function(cell) {
                return cell.y + 1;
            }
        }
    }
};
