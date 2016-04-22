'use strict';

class PaginationMgr {
    constructor() {
        this.previousPage = this.currentPage = this.totalPages = this.nextPage = 1;

        this.$html = $(this.constructor.resources.layout);

        this.$currentPage = this.$html.find('span#data-page-current');
        this.$totalPages  = this.$html.find('span#data-page-total');

        this.$nextPageBtn     = this.$html.find('button#next-page-btn');
        this.$previousPageBtn = this.$html.find('button#previous-page-btn');
    }

    /**
     * @param {int} currPage
     * @param {int} totalPages
     */
    update(currPage, totalPages) {
        this.setTotalPages(totalPages);
        this.setCurrentPage(currPage);

        this.setPreviousPage(this.currentPage - 1);
        this.setNextPage(this.currentPage + 1);
    }

    /**
     * @param {int} page
     */
    setPreviousPage(page) {
        this.previousPage = page;

        this.$previousPageBtn.attr("data-page", this.previousPage)[0].disabled = this.previousPage < 1;
    }

    /**
     * @param {int} page
     */
    setCurrentPage(page) {
        this.currentPage = page;

        this.$currentPage.text(this.currentPage);
    }

    /**
     * @param {int} page
     */
    setTotalPages(page) {
        this.totalPages = page;

        this.$totalPages.text(this.totalPages);
    }

    /**
     * @param {int} page
     */
    setNextPage(page) {
        this.nextPage = page;

        this.$nextPageBtn.attr("data-page", this.nextPage)[0].disabled = this.nextPage > this.totalPages;
    }
}

PaginationMgr.resources = {
    /** @type {string} */
    layout: '\
        <div class="pagination-area"> \
            <div class="btn-group btn-group-xs" role="group" aria-label="statistics-pagination"> \
                <button type="button" id="previous-page-btn" data-page="" class="btn btn-default"> \
                    <span id="data-page-previous" class="glyphicon glyphicon-chevron-left"></span> \
                </button> \
                <button type="button" class="btn btn-default" disabled="disabled"> \
                    <span id="data-page-current"></span> \
                    <span> of </span> \
                    <span id="data-page-total""></span> \
                </button> \
                <button type="button" id="next-page-btn" data-page="" class="btn btn-default"> \
                    <span id="data-page-next" class="glyphicon glyphicon-chevron-right"></span> \
                </button> \
            </div> \
        </div>'
};
