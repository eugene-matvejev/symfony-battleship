function StatisticsMgr() {
    this.apiMgr   = new APIMgr();
    this.$area    = $('div#stats-area');
}

StatisticsMgr.prototype = {
    fetch: function() {
        var self = this;
        this.apiMgr.request(self.$area.attr(StatisticsMgr.resources.link), 'GET', undefined, function(json) { self.updateHTML(json); });
    },
    updateHTML: function(json) {
        var $table = this.getTableHTML().append({});

        for(var i in json) {
            $table.append(this.getRowHTML(json[i]));
        }

        this.$area.html($table);
    },
    getTableHTML: function() {
        return $($.parseHTML(
           '<table class="table">' +
                '<tr>' +
                   '<th>' + StatisticsMgr.text.id + '</th>' +
                   '<th>' + StatisticsMgr.text.gameStart + '</th>' +
                   '<th>' + StatisticsMgr.text.gameEnded + '</th>' +
                   '<th>' + StatisticsMgr.text.winner + '</th>' +
                '</tr>' +
           '</table>'
        ));
    },
    getRowHTML: function(json) {
        return $($.parseHTML(
            '<tr>' +
                '<td>' + json.id + '</td>' +
                '<td>' + json.time1 + '</td>' +
                '<td>' + json.time2 + '</td>' +
                '<td>' + json.winner.name + '</td>' +
            '</tr>'));
    }
};


StatisticsMgr.resources = { link: 'data-stats-link' };
StatisticsMgr.text = {
    id: 'id',
    gameStart: 'Game started at',
    gameEnded: 'Game finished at',
    winner: 'Winner'
};