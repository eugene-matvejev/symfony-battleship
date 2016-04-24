'use strict';

class GameResults extends APIRequestMgr {
    /**
     * @param {jQuery} $el
     */
    constructor($el) {
        super();

        this.pagination = new PaginationMgr();
        this.route      = $el.attr('data-game-results-link');

        this.$tableArea = $(this.constructor.resources.layout);
        $el.append(this.$tableArea, this.pagination.$html);
    }

    /**
     * @param {int|string} page
     */
    fetch(page) {
        let self      = this,
            onSuccess = function (response) {
                self.update(response);
            };

        super.request('GET', this.route + page, undefined, onSuccess);
    }

    /**
     * @param {{meta: {currentPage: {int}, totalPages: {int}}, results: []}} response
     */
    update(response) {
        let html   = this.constructor.resources.html,
            $table = $(html.table()),
            $tBody = $table.find('tbody');

        response.results.forEach(result => $tBody.append(html.row(result)));

        this.$tableArea.html($table);
        this.pagination.update(response.meta.currentPage, response.meta.totalPages);
    }
}

GameResults.resources = {};
/** @enum {string} */
GameResults.resources.tableHeader = {
    resultId: '#',
    playerName: 'winner',
    finishTime: 'finished at'
};
GameResults.resources.layout = '<div class="results-area"></div>';
GameResults.resources.html   = {
    /**
     * @returns {string}
     */
    table: function () {
        let header = GameResults.resources.tableHeader;

        return ` \
            <table class="table"> \
                <tr> \
                    <th>${header.resultId}</th> \
                    <th>${header.playerName}</th> \
                    <th>${header.finishTime}</th> \
                </tr> \
            </table>`;
    },
    /**
     * @param {{id: {int}, player: {id: {int}}, timestamp: {string}}} obj
     *
     * @returns {string}
     */
    row: function (obj) {
        return ` \
            <tr> \
                <td>${obj.id}</td> \
                <td>${(new Date(obj.timestamp)).toLocaleString()}</td> \
                <td>${obj.player.name}</td> \
            </tr>`;
    }
};
