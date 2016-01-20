function Battlefield(size, $el) {
    this.id    = 'unk';
    this.$html = $el;
    this.size  = size;
    this.cells = new CellContainer();

    this.initData().htmlUpdate();
}

Battlefield.prototype = {
    initData: function() {
        for(var y = 0; y < this.size; y++) {
            var cells = [];

            this.cells.navX.push(y);
            this.cells.navY.push(y);

            for(var x = 0; x < this.size; x++) {
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

        for(var y in this.cells.navY) {
            var $row = _$row.clone();

            $top.append(_layout(undefined, undefined, undefined, _format.xAxis(this.cells.navX[y])));
            $row.append(_layout(undefined, undefined, undefined, _format.yAxis(this.cells.navY[y])));

            for(var x in this.cells.data[y]) {
                $row.append(this.cells.data[y][x].$html);
            }

            $row.append(_layout(undefined, undefined, undefined, _format.yAxis(this.cells.navY[y])));
            this.$html.append($row);
        }

        this.$html.append($top.clone());
    },
    getCell: function(x, y) {
        for(var _y in this.cells.data) {
            if(y != _y)
                continue;
            for(var _x in this.cells.data[_y]) {
                if(x == _x) {
                    return this.cells.data[_y][_x];
                }
            }
        }
        return undefined;
    },
    mockData: function() {
        var _config = Cell.resources.config;

        this.getCell(0, 1).setState(_config.state.shipDied);
        this.getCell(0, 2).setState(_config.state.shipLive);
        this.getCell(0, 3).setState(_config.state.shipLive);

        this.getCell(2, 2).setState(_config.state.shipLive);
        this.getCell(2, 3).setState(_config.state.shipLive);

        this.getCell(2, 5).setState(_config.state.shipLive);

        this.getCell(2, 0).setState(_config.state.shipLive);
        this.getCell(3, 0).setState(_config.state.shipLive);
        this.getCell(4, 0).setState(_config.state.shipLive);
        this.getCell(5, 0).setState(_config.state.shipLive);

        this.getCell(5, 4).setState(_config.state.shipLive);
        this.getCell(5, 5).setState(_config.state.shipLive);

        this.getCell(4, 2).setState(_config.state.shipLive);
    }
};

Battlefield.resources = {
    config: {
        format: {
            xAxis: function (i) {
                return String.fromCharCode(i + 97);
            },
            yAxis: function (i) {
                return i + 1;
            }
        }
    }
};