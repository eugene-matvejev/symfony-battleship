$(document).ready(function() {
        players = [
            {id: 1, name: 'CPU'},
            {id: 2, name: 'Human 1'},
            {id: 3, name: 'Human 2'},
            {id: 4, name: 'Human 3'}
        ];
    var battle = new Battle(players);
        battle.init();

    console.log(battle);

    $('#battle-area')
        .on('click', '.battlefield-cell', function(e) {
            e.stopPropagation();
            //var $el = $(this);
            //console.log(this);
            battle.update(this);
        });
    //battleArea = new BattleArea();
    //battleArea.init();
});
//
//
//function BattleArea() {
//    this.$area  = $("#battle-area");
//    this.fields = new BattleData();
//    this.states = new FieldState();
//}
//BattleArea.prototype = {
//    size: 10,
//    getHTMLField: function(txt) {
//        return $($.parseHTML('<div class="col-md-1 battle-field" data-x="unk" data-y="unk" data-s="unk">' + (txt !== undefined ? txt : '') + '</div>'));
//    },
//    init: function() {
//        this.initFields();
//        this.mockData();
//        this.initHTML();
//    },
//    initFields: function() {
//        var dataRow = [];
//        for(var i = 0; i < this.size; i++) {
//            this.fields.navX.push(i);
//            this.fields.navY.push(this.getXName(i));
//            for(var j = 0; j < this.size; j++) {
//                dataRow.push(new BattleField(i, j, this.states.waterLive));
//            }
//            this.fields.data.push(dataRow.slice());
//            dataRow = [];
//        }
//        console.log(this.fields);
//        console.log(this.fields.navX, this.fields.navY, this.fields.data);
//    },
//    mockData: function() {
//        this.fields.data[3][4].state =
//        this.fields.data[3][5].state =
//        this.fields.data[3][6].state = this.states.shipLive;
//        this.fields.data[5][4].state =
//        this.fields.data[5][5].state =
//        this.fields.data[5][6].state = this.states.shipLive;
//        this.fields.data[7][4].state =
//        this.fields.data[7][5].state =
//        this.fields.data[7][6].state = this.states.shipLive;
//    },
//    initHTML: function() {
//        var row   = $($.parseHTML('<div class="row"></div>')),
//            xAxis = row.clone().attr('data-dimension', 'x'), //$($.parseHTML('<div class="axis"></div>')).attr('data-dimension', 'x');
//            yAxis = row.clone().attr('data-dimension', 'y');
//
//        xAxis.append(this.getHTMLField());
//        for(var i in this.fields.navX) {
//            xAxis.append(this.getHTMLField(this.fields.navX[i]+1));
//        }
//        this.$area.html(xAxis);
//
//        for(var i in this.fields.navY) {
//            var rowNew = row.clone();
//
//            rowNew.append(this.getHTMLField(this.fields.navY[i]));
//            for(var j in this.fields.data[i]) {
//                var x = this.fields.data[i][j].x,
//                    y = this.fields.data[i][j].y,
//                    s = this.fields.data[i][j].state;
//                console.log(this.fields.data[i]);
//                rowNew.append(this.getHTMLField().attr('data-x', x).attr('data-y', j).attr('data-s', s));
//            }
//            this.$area.append(rowNew.clone());
//        }
//    },
//    getXName: function(i) {
//        return String.fromCharCode(i+97);
//    }
//};
//
//
//function BattleData() {
//
//}
//BattleData.prototype = {
//    navX: [],
//    navY: [],
//    data: []
//};
//
//
//function BattleField(x, y, state) {
//    this.x     = x;
//    this.y     = y;
//    this.state = state;
//}
//BattleField.prototype = {
//
//};
//
