function Statistics() {
    this.apiMgr   = new APIMgr();
    this.$area    = $('div#stats-area');
}

Statistics.prototype = {
    fetch: function() {
        var self = this;
        this.apiMgr.request(self.$area.attr(Statistics.resources.link), 'GET', undefined, function(json) { self.updateHTML(json); });
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
                   '<th>' + Statistics.text.id + '</th>' +
                   '<th>' + Statistics.text.gameStart + '</th>' +
                   '<th>' + Statistics.text.gameEnded + '</th>' +
                   '<th>' + Statistics.text.winner + '</th>' +
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


Statistics.resources = { link: 'data-stats-link' };
Statistics.text = {
    id: 'id',
    gameStart: 'Game started at',
    gameEnded: 'Game finished at',
    winner: 'Winner'
};