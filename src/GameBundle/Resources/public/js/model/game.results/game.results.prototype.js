'use strict';

/**
 * @param {jQuery} $el
 *
 * @constructor
 */
function GameResults($el) {
    this.$html = $el;
    this.apiMgr = new APIRequestMgr();
    this.pageMgr = new PageMgr();
    this.pagination = new PaginationMgr(this.$html);
}

/**
 * @property {APIRequestMgr} apiMgr
 * @property {PageMgr}       PageMgr
 * @property {PaginationMgr} pagination
 *
 * @property {jQuery}        $html
 */
GameResults.prototype = {
    /**
     * @param {int|string} page
     */
    fetch: function (page) {
        let self = this,
            requestUrl = this.$html.attr(GameResults.resources.config.attribute.route) + page,
            onSuccess = function (response) {
                self.updateHTML(response);
            };

        this.apiMgr.request('GET', requestUrl, undefined, onSuccess);
    },
    /**
     * @param {{meta: {currentPage: {int}, totalPages: {int}}, results: []}} response
     */
    updateHTML: function (response) {
        let html = GameResults.resources.html,
            $table = $(html.layout());

        response.results.map(result => $table.append(html.row(result)));

        this.$html.html($table).append(PaginationMgr.resources.html.layout());
        this.pagination.updateHTML(response.meta.currentPage, response.meta.totalPages);
    }
};

GameResults.resources = {};
GameResults.resources.config = {
    /** @enum {string} */
    attribute: {
        route: 'data-game-results-link'
    }
};
/** @enum {string} */
GameResults.resources.text = {
    id: 'id',
    player: 'winner',
    time: 'finished at'
};
GameResults.resources.html = {
    /**
     * @returns {string}
     */
    layout: function () {
        let text = GameResults.resources.text;

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
    row: function (obj) {
        let date = new Date(obj.timestamp);

        return '' +
            '<tr>' +
                '<td>' + obj.id + '</td>' +
                '<td>' + date.toLocaleString() + '</td>' +
                '<td>' + obj.player.name + '</td>' +
            '</tr>';
    }
};
