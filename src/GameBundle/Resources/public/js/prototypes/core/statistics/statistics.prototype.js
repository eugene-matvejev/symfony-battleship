function Statistics() {
    this.apiMgr     = new APIMgr();
    this.pageMgr    = new PageMgr();
    this.$area      = $('div#stats-area');
    this.$paginator = new UI(this.$area, undefined, undefined, undefined, undefined);
}

Statistics.prototype = {
    fetch: function(page) {
        var self = this,
            url  = this.$area.attr(Statistics.resources.config.route.data) + page;

        console.log(url);
        this.pageMgr.loadingMode(true);

        this.apiMgr.request('GET', url, undefined,
            function(json) {
                self.htmlUpdate(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    htmlUpdate: function(json) {
        var $table = this.htmlTable(),
            page = json.meta.page;
        for(var i in json.data) {
            $table.append(this.htmlRow(json.data[i]));
        }

        this.$area.html($table);
        console.log(json);
        this.$paginator.htmlUpdate(page.curr, page.total);
    },
    htmlRow: function(json) {
        return '<tr>' +
                    '<td>' + json.id + '</td>' +
                    '<td>' + json.time.s + '</td>' +
                    '<td>' + json.time.f + '</td>' +
                    '<td>' + json.player.name + '</td>' +
                '</tr>';
    },
    htmlTable: function() {
        var text = Statistics.resources.config.text;

        return $($.parseHTML(
            '<table class="table">' +
                '<tr>' +
                    '<th>' + text.id + '</th>' +
                    '<th>' + text.gameStart + '</th>' +
                    '<th>' + text.gameEnded + '</th>' +
                    '<th>' + text.winner + '</th>' +
                '</tr>' +
            '</table>'
        ));
    }
};

Statistics.resources = {
    config: {
        text: {
            id: 'id',
            winner: 'Winner',
            gameStart: 'Game started at',
            gameEnded: 'Game finished at'
        },
        route: {
            data: 'data-stats-link'
        }
    }
};
