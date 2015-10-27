function Battlefield(size) {
    this.id    = 'unk';
    this.size  = size;
    this.cells = new CellContainer();
}

Battlefield.prototype = {
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
                cells.push(new Cell(x, y, Cell.state.seaLive));
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
    getCell: function(x, y) {
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
        this.cells.data[0][2].s = Cell.state.shipLive;
        this.cells.data[0][3].s =
        this.cells.data[0][4].s = Cell.state.shipDied;
    }
};

Battlefield.formatXAxis = function(i) {
    return i + 1;
};
Battlefield.formatYAxis = function(i) {
    return String.fromCharCode(i + 97);
};
