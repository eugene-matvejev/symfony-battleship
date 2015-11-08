function Battlefield(size, $el) {
    this.id    = 'unk';
    //this.$area = $el;
    this.$html = $el;
    this.size  = size;
    this.cells = new CellContainer();
    this.initModules();
    this.initData();
}

Battlefield.prototype = {
    initData: function() {
        for(var x = 0; x < this.size; x++) {
            var cells = [];

            this.cells.navX.push(x);
            this.cells.navY.push(x);

            for(var y = 0; y < this.size; y++) {
                cells.push(new Cell(x, y, Cell.resources.config.state.seaLive));
            }

            this.cells.data.push(cells);
        }

        return this;
    },
    initModules: function() {
        this.html.super = this;

        return this;
    },
    html: {
        update: function() {
            var _layout = Cell.resources.html.layout,
                $row    = CellContainer.resources.html.layout(),
                xAxis   = $row.clone().append(_layout(undefined, undefined, undefined, undefined));

            //for(var i in this.super.cells.navX) {
            //}
            this.$html.html(xAxis);

            for(var i in this.super.cells.navY) {
                var html = $row.clone();

                xAxis.append(_layout(undefined, undefined, undefined, Battlefield.formatXAxis(this.cells.navX[i])));
                html.append(_layout(undefined, undefined, undefined, Battlefield.formatYAxis(this.cells.navY[i])));
                for(var j in this.cells.data[i]) {
                    var cell = this.cells.data[i][j];

                    html.append(cell.getHTML());
                }
                this.$html.append(html);
            }
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
        this.cells.data[0][2].s = Cell.resources.config.state.shipLive;
        this.cells.data[0][3].s =
        this.cells.data[0][4].s = Cell.resources.config.state.shipDied;
    }
};

Battlefield.formatXAxis = function(i) {
    return i + 1;
};
Battlefield.formatYAxis = function(i) {
    return String.fromCharCode(i + 97);
};
