'use strict';

/**
 * @constructor
 */
function Statistics() {
    this.$html      = $('div#stats-area');
    this.apiMgr     = new APIMgr();
    this.pageMgr    = new PageMgr();
    this.$paginator = new UI(this.$html, undefined, undefined, undefined, undefined);
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
     * @param {Object} response
     */
    updateHTML: function(response) {
        let html   = Statistics.resources.html,
            page   = response.meta.page,
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
    /**
     * @enum {string}
     */
    time: {
        start: 'Game started at',
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
