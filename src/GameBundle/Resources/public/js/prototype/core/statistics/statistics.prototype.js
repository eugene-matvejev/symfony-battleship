'use strict';

/**
 * @constructor
 */
function Statistics() {
    this.apiMgr     = new APIMgr();
    this.pageMgr    = new PageMgr();
    this.$html      = $('div#stats-area');
    this.$paginator = new UI(this.$html, undefined, undefined, undefined, undefined);
}

Statistics.prototype = {
    /**
     * @param {int} page
     */
    fetch: function(page) {
        let self = this,
            url  = this.$html.attr(Statistics.resources.config.attribute.route) + page;

        this.pageMgr.loadingMode(true);

        this.apiMgr.request('GET', url, undefined,
            function(json) {
                self.updateHTML(json);
                self.pageMgr.loadingMode(false);
            }
        );
    },
    /**
     * @param {Object} response
     *
     * @returns {void}
     */
    updateHTML: function(response) {
        let page   = response.meta.page,
            html   = Statistics.resources.html,
            $table = $(html.table());

        response.data.every(el => $table.append(html.row(el)));

        this.$html.html($table);
        this.$paginator.htmlUpdate(page.curr, page.total);
    }
};

Statistics.resources = {};
Statistics.resources.config = {
    attribute: {
        /**
         * @type {string}
         */
        route: 'data-stats-link'
    }
};
Statistics.resources.text = {
    /**
     * @type {string}
     */
    id: 'id',
    /**
     * @type {string}
     */
    winner: 'Winner',
    time: {
        /**
         * @type {string}
         */
        start: 'Game started at',
        /**
         * @type {string}
         */
        finish: 'Game started at'
    }
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
                    '<th>' + text.time.start + '</th>' +
                    '<th>' + text.time.finish + '</th>' +
                    '<th>' + text.winner + '</th>' +
                '</tr>' +
            '</table>';
    },
    /**
     * @param {Object} obj
     *
     * @returns {string}
     */
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
