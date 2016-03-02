'use strict';

function Statistics() {
    this.apiMgr     = new APIMgr();
    this.pageMgr    = new PageMgr();
    this.$area      = $('div#stats-area');
    this.$paginator = new UI(this.$area, undefined, undefined, undefined, undefined);
}

Statistics.prototype = {
    fetch: function(page) {
        let self = this,
            url  = this.$area.attr(Statistics.resources.config.route.data) + page;

        this.pageMgr.loadingMode(true);

        this.apiMgr.request('GET', url, undefined,
            function(json) {
                self.htmlUpdate(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    htmlUpdate: function(response) {
        let resources = Statistics.resources,
            page      = response.meta.page,
            $table    = $(resources.html.table());

        response.data.every(el => $table.append(resources.html.row(el)));

        this.$area.html($table);
        this.$paginator.htmlUpdate(page.curr, page.total);
    }
};

Statistics.resources = {};
Statistics.resources.config = {
    route: {
        data: 'data-stats-link'
    }
};
Statistics.resources.text = {
    id: 'id',
    winner: 'Winner',
    time: {
        start: 'Game started at',
        finish: 'Game started at'
    }
};
Statistics.resources.html = {
    table: function() {
        let text = Statistics.resources.text;

        return '' +
            '<table class="table">' +
                '<tr>' +
                    '<th>' + text.id + '</th>' +
                    '<th>' + text.time.start + '</th>' +
                    '<th>' + text.time.finish + '</th>' +
                    '<th>' + text.winner + '</th>' +
                '</tr>' +
            '</table>';
    },
    row: function(obj) {
        return '' +
            '<tr>' +
                '<td>' + obj.id + '</td>' +
                '<td>' + obj.time.s + '</td>' +
                '<td>' + obj.time.f + '</td>' +
                '<td>' + obj.player.name + '</td>' +
            '</tr>';
    }
};
