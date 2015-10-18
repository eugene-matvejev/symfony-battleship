function Battlefield() {
    this.id    = 'unk';
    this.cells = new CellContainer();
}

Battlefield.prototype = {
    size: 10,
    $area: undefined,
    setArea: function($el) {
        this.$area = $el;
        return this;
    },
    initData: function() {
        for(var x = 0; x < this.size; x++) {
            var cells = [];

            this.cells.navX.push(x);
            this.cells.navY.push(x);

            for(var y = 0; y < this.size; y++) {
                cells.push(new Cell(x, y, Cell.states.seaLive));
            }

            this.cells.data.push(cells);
        }
    },
    updateHTML: function() {
        var cellContainer = CellContainer.getHTML(),
            xAxis         = cellContainer.clone().append(Cell.getHTML(undefined, undefined, undefined, undefined));

        for(var i in this.cells.navX) {
            xAxis.append(Cell.getHTML(undefined, undefined, undefined, Battlefield.formatXAxis(this.cells.navX[i])));
        }
        this.$area.html(xAxis);

        for(var i in this.cells.navY) {
            var html = cellContainer.clone();

            html.append(Cell.getHTML(undefined, undefined, undefined, Battlefield.formatYAxis(this.cells.navY[i])));
            for(var j in this.cells.data[i]) {
                var cell = this.cells.data[i][j];

                html.append(cell.getHTML());
            }
            this.$area.append(html);
        }
    },
    getCellData: function(x, y) {
        for(var _x in this.cells.data) {
            if(x != _x)
                continue;
            for(var _y in this.cells.data[_x]) {
                if(y == _y) {
                    return this.cells.data[_x][_y];
                }
            }
        }
        return undefined;
    },
    mockData: function() {
        this.cells.data[1][4].s =
        this.cells.data[1][5].s =
        this.cells.data[1][6].s = Cell.states.shipDied;

        this.cells.data[3][4].s =
        this.cells.data[3][5].s =
        this.cells.data[3][6].s =

        this.cells.data[5][4].s =
        this.cells.data[5][5].s =
        this.cells.data[5][6].s =

        this.cells.data[7][4].s =
        this.cells.data[7][5].s =
        this.cells.data[7][6].s = Cell.states.shipLive;
    }
};

Battlefield.formatXAxis = function(i) {
    return i + 1;
};
Battlefield.formatYAxis = function(i) {
    return String.fromCharCode(i + 97);
};