function Statistics() {
    this.apiMgr     = new APIMgr();
    this.pageMgr    = new PageMgr();
    this.$area      = $('div#stats-area');
    this.$paginator = new UI(this.$area, undefined, undefined, undefined, undefined);
}

Statistics.prototype = {
    fetch: function(page) {

        page = page !== undefined ? page : 1;

        var self = this,
            url  = this.$area.attr(Statistics.resources.config.route.data) + page;
        this.pageMgr.loadingMode(true);

        this.apiMgr.request('GET', url, undefined,
            function(json) {
                self.htmlUpdate(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    htmlUpdate: function(json) {
        var $table = this.htmlTable();
        for(var i in json) {
            $table.append(this.htmlRow(json[i]));
        }
        var $pagination = this.htmlPaginator(1, 2, 1, 3);
        console.log($pagination[0]);
        this.$area.html($table);
        this.$area.append($pagination);
    },
    htmlRow: function(json) {
        return $($.parseHTML(
            '<tr>' +
                '<td>' + json.id + '</td>' +
                '<td>' + json.time1 + '</td>' +
                '<td>' + json.time2 + '</td>' +
                '<td>' + json.winner.name + '</td>' +
            '</tr>'
        ));
    },
    htmlTable: function() {
        var _text = Statistics.resources.config.text;

        return $($.parseHTML(
            '<table class="table">' +
                '<tr>' +
                    '<th>' + _text.id + '</th>' +
                    '<th>' + _text.gameStart + '</th>' +
                    '<th>' + _text.gameEnded + '</th>' +
                    '<th>' + _text.winner + '</th>' +
                '</tr>' +
            '</table>'
        ));
    },
    htmlPaginator: function(currPage, nextPage, prevPage, totalPages) {
        return $($.parseHTML(
            '<div class="pagination-area">' +
                '<div class="btn-group btn-group-xs" role="group" aria-label="statistics-pagination">' +
                    '<button type="button" data-page="' + prevPage + '" class="btn btn-default">' +
                        '<span class="glyphicon glyphicon-chevron-left"></span>' +
                    '</button>' +
                    '<button type="button" data-page="' + currPage + '" class="btn btn-default" disabled="disabled">' +
                        '<span>' + currPage + '</span>' +
                        '<span> of </span>' +
                        '<span>' + totalPages + '</span>' +
                    '</button>' +
                    '<button type="button" data-page="' + nextPage + '" class="btn btn-default">' +
                        '<span class="glyphicon glyphicon-chevron-right"></span>' +
                    '</button>' +
                '</div>' +
            '</div>'
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
