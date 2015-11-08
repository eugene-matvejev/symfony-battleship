function Statistics() {
    this.apiMgr   = new APIMgr();
    this.pageMgr  = new PageMgr();
    this.$area    = $('div#stats-area');
    this.initModules();
}

Statistics.prototype = {
    fetch: function() {
        var self = this;
        this.pageMgr.loadingMode(true);

        this.apiMgr.request('GET', self.$area.attr(Statistics.resources.config.route.data), undefined,
            function(json) {
                self.html.update(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    initModules: function() {
        this.html.super = this;
    },
    html: {
        update: function(json) {
            var $table = this.table();
            for(var i in json) {
                $table.append(this.row(json[i]));
            }

            this.super.$area.html($table);
        },
        row: function(json) {
            return $($.parseHTML(
                '<tr>' +
                    '<td>' + json.id + '</td>' +
                    '<td>' + json.time1 + '</td>' +
                    '<td>' + json.time2 + '</td>' +
                    '<td>' + json.winner.name + '</td>' +
                '</tr>'
            ));
        },
        table: function() {
            return $($.parseHTML(
                '<table class="table">' +
                    '<tr>' +
                        '<th>' + Statistics.resources.config.text.id + '</th>' +
                        '<th>' + Statistics.resources.config.text.gameStart + '</th>' +
                        '<th>' + Statistics.resources.config.text.gameEnded + '</th>' +
                        '<th>' + Statistics.resources.config.text.winner + '</th>' +
                    '</tr>' +
                '</table>'
            ));
        }
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
