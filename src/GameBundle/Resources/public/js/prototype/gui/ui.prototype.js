'use strict';

/**
 * @param {jQuery} $el
 *
 * @constructor
 */
function UI($el) {
    this.$area = $el;
}

/**
 * @property {jQuery} $area
 */
UI.prototype = {
    htmlUpdate: function(currPage, totalPages) {
        var attr = UI.resources.config.attr,
            nextPage = currPage + 1 > totalPages ? totalPages : currPage + 1,
            prevPage = currPage - 1 <= 0 ? 1 : currPage - 1;

        if(this.$area.find('>.pagination-area').length < 1) {
            this.$area.append(UI.resources.html.layout());
        }

        this.$area.find('button[type="button"]').each(function() {
            switch(this.id) {
                case attr.id.curr:
                    this.setAttribute(attr.page, currPage);
                    $(this).find('>span:first-child').html(currPage);
                    $(this).find('>span:last-child').html(totalPages);
                    break;
                case attr.id.prev:
                    if(currPage == 1) {
                        this.setAttribute('disabled', 'disabled');
                    }
                    this.setAttribute(attr.page, prevPage);
                    break;
                case attr.id.next:
                    if(currPage == totalPages) {
                        this.setAttribute('disabled', 'disabled');
                    }
                    this.setAttribute(attr.page, nextPage);
                    break;
            }
        });
    }
};

UI.resources = {};
UI.resources.config = {
    attr: {
        /**
         * @enum {string}
         */
        id: {
            prev: 'prev',
            curr: 'curr',
            next: 'next',
            total: 'total'
        },
        /**
         * @type {string}
         */
        page: 'data-page'
    }
};
UI.resources.html = {
    /**
     * @returns {string}
     */
    layout: function() {
        let attr = UI.resources.config.attr;

        return '' +
            '<div class="pagination-area">' +
                '<div class="btn-group btn-group-xs" role="group" aria-label="statistics-pagination">' +
                    '<button id="' + attr.id.prev + '" type="button" class="btn btn-default">' +
                        '<span class="glyphicon glyphicon-chevron-left"></span>' +
                    '</button>' +
                    '<button id="' + attr.id.curr + '" type="button" class="btn btn-default" disabled="disabled">' +
                        '<span></span>' +
                        '<span> of </span>' +
                        '<span></span>' +
                    '</button>' +
                    '<button id="' + attr.id.next + '" type="button" class="btn btn-default">' +
                        '<span class="glyphicon glyphicon-chevron-right"></span>' +
                    '</button>' +
                '</div>' +
            '</div>';
    }
};
