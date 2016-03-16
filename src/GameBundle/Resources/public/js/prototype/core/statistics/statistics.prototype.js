'use strict';

/**
 * @constructor
 */
function Statistics() {
    this.$html     = $('div#stats-area');
    this.apiMgr    = new APIMgr();
    this.pageMgr   = new PageMgr();
    this.paginator = new UI(this.$html, undefined, undefined, undefined, undefined);
}

/**
 * @property {jQuery} $html
 */
Statistics.prototype = {
    /**
     * @param {int} page
     */
    fetch: function(page) {
        let self = this,
            url  = this.$html.attr(Statistics.resources.config.attribute.route) + page;

        this.apiMgr.request('GET', url, undefined,
            function(response) {
                self.updateHTML(response);
            },
            function(response) {
            }
        );
    },
    /**
     * @param {{meta: {currentPage: {int}, totalPages: {int}}, results: []}} response
     */
    updateHTML: function(response) {
        let html   = Statistics.resources.html,
            meta   = response.meta,
            $table = $(html.table());

        response.results.every(el => $table.append(html.row(el)));

        this.$html.html($table);
        this.paginator.htmlUpdate(meta.currentPage, meta.totalPages);
    }
};

Statistics.resources = {};
Statistics.resources.config = {
    /**
     * @enum {string}
     */
    attribute: {
        route: 'data-stats-link'
    }
};
/**
 * @enum {string}
 */
Statistics.resources.text = {
    id: 'id',
    player: 'winner',
    time: 'finished at'
};
Statistics.resources.html = {
    /**
     * @returns {string}
     */
    table: function() {
        let text = Statistics.resources.text;

        return '' +
            '<table class="table">' +
                '<tr>' +
                    '<th>' + text.id + '</th>' +
                    '<th>' + text.time + '</th>' +
                    '<th>' + text.player + '</th>' +
                '</tr>' +
            '</table>';
    },
    /**
     * @param {{id: {int}, player: {id: {int}}, timestamp: {string}}} obj
     *
     * @returns {string}
     */
    row: function(obj) {
        let date = new Date(obj.timestamp);

        return '' +
            '<tr>' +
                '<td>' + obj.id + '</td>' +
                '<td>' + date.toLocaleString() + '</td>' +
                '<td>' + obj.player.name + '</td>' +
            '</tr>';
    }
};
