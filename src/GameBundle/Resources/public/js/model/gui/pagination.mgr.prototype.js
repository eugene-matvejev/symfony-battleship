'use strict';

class PaginationMgr {
    /**
     * @param {jQuery} $el
     */
    constructor($el) {
        this.$area = $el;
    }

    /**
     * @param {int|string} currPage
     * @param {int|string} totalPages
     */
    updateHTML(currPage, totalPages) {
        let attr     = PaginationMgr.resources.config.attr,
            nextPage = currPage + 1 > totalPages ? totalPages : currPage + 1,
            prevPage = currPage - 1 <= 0 ? 1 : currPage - 1;

        this.$area.find('button[type="button"]').each(function () {
            switch (this.id) {
                case attr.id.curr:
                    $(this).find('>span:first-child').html(currPage);
                    $(this).find('>span:last-child').html(totalPages);
                    this.setAttribute(attr.page, currPage);
                    break;
                case attr.id.prev:
                    if (currPage == 1) {
                        this.setAttribute('disabled', 'disabled');
                    }
                    this.setAttribute(attr.page, prevPage);
                    break;
                case attr.id.next:
                    if (currPage === totalPages) {
                        this.setAttribute('disabled', 'disabled');
                    }
                    this.setAttribute(attr.page, nextPage);
                    break;
            }
        });
    }
}

PaginationMgr.resources        = {};
PaginationMgr.resources.config = {
    attr: {
        /** @enum {string} */
        id: {
            prev: 'prev',
            curr: 'curr',
            next: 'next',
            total: 'total'
        },
        /** @type {string} */
        page: 'data-page'
    }
};
PaginationMgr.resources.html   = {
    /**
     * @returns {string}
     */
    layout: function () {
        let attr = PaginationMgr.resources.config.attr;

        return ' \
            <div class="pagination-area"> \
                <div class="btn-group btn-group-xs" role="group" aria-label="statistics-pagination"> \
                    <button id="' + attr.id.prev + '" type="button" class="btn btn-default"> \
                        <span class="glyphicon glyphicon-chevron-left"></span> \
                    </button> \
                    <button id="' + attr.id.curr + '" type="button" class="btn btn-default" disabled="disabled"> \
                        <span></span> \
                        <span> of </span> \
                        <span></span> \
                    </button> \
                    <button id="' + attr.id.next + '" type="button" class="btn btn-default"> \
                        <span class="glyphicon glyphicon-chevron-right"></span> \
                    </button> \
                </div> \
            </div>';
    }
};
