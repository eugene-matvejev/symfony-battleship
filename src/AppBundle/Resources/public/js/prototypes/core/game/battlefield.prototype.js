function Battlefield(size, $el) {
    this.id    = 'unk';
    this.$html = $el;
    this.size  = size;
    this.cells = new CellContainer();

    this.initData().htmlUpdate();
}

Battlefield.prototype = {
    initData: function() {
        for(var x = 0; x < this.size; x++) {
            var cells = [];

            this.cells.navX.push(x);
            this.cells.navY.push(x);

            for(var y = 0; y < this.size; y++) {
                cells.push(new Cell(x, y, undefined));
            }

            this.cells.data.push(cells);
        }

        return this;
    },
    htmlUpdate: function() {
        var _layout = Cell.resources.html.layout,
            _format = Battlefield.resources.config.format,
            _$row   = CellContainer.resources.html.layout(),
            $top    = _$row.clone().append(_layout());

        this.$html.html($top);

        for(var x in this.cells.navX) {
            var $row = _$row.clone();

            $top.append(_layout(undefined, undefined, undefined, _format.xAxis(this.cells.navX[x])));
            $row.append(_layout(undefined, undefined, undefined, _format.yAxis(this.cells.navY[x])));

            for(var y in this.cells.data[x]) {
                $row.append(this.cells.data[x][y].$html);
            }

            $row.append(_layout(undefined, undefined, undefined, _format.yAxis(this.cells.navY[x])));
            this.$html.append($row);
        }

        this.$html.append($top.clone());
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
        var _config = Cell.resources.config;

        this.getCell(0, 2).setState(_config.state.shipLive);
        this.getCell(0, 3).setState(_config.state.shipDied);
        this.getCell(0, 4).setState(_config.state.shipDied);
        this.getCell(0, 5).setState(_config.state.shipDied);
    }
};

Battlefield.resources = {
    config: {
        format: {
            xAxis: function (i) {
                return i + 1;
            },
            yAxis: function (i) {
                return String.fromCharCode(i + 97);
            }
        }
    }
};