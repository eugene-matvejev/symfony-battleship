function UI($el, currPage, nextPage, prevPage, totalPages) {
    this.$area      = $el;

    this.currPage   = currPage !== undefined ? currPage : undefined;
    this.nextPage   = nextPage !== undefined ? nextPage : undefined;
    this.prevPage   = prevPage !== undefined ? prevPage : undefined;
    this.totalPages = totalPages !== undefined ? totalPages : undefined;
}

UI.prototype = {
    getCurrPage: function() {
        return this.currPage;
    },
    setCurrPage: function(page) {
        this.currPage = page;

        return this;
    },
    getNextPage: function() {
        return this.nextPage;
    },
    setNextPage: function(page) {
        this.nextPage = page;

        return this;
    },
    getPrevPage: function() {
        return this.prevPage;
    },
    setPrevPage: function(page) {
        this.prevPage = page;

        return this;
    },
    getTotalPage: function() {
        return this.totalPages;
    },
    setTotalPage: function(amount) {
        return this.totalPages = amount;
    },
    htmlUpdate: function(currPage, nextPage, prevPage, totalPages) {
        var attr = UI.resources.config.attr;
        if(this.$area.find('>.pagination-area').length > 0) {
            this.$area.find('button.button').each(function() {
                switch(this.id) {
                    case attr.id.curr:
                        this.setAttribute(attr.page, currPage);
                        $(this).find('>span:first-child').html(currPage);
                        $(this).find('>span:last-child').html(totalPages);
                        break;
                    case attr.id.prev:
                        this.setAttribute(attr.page, prevPage);
                        break;
                    case attr.id.next:
                        this.setAttribute(attr.page, nextPage);
                        break;
                }
            });
        } else {
            this.$area.html(UI.resources.html.layout(currPage, nextPage, prevPage, totalPages));
        }
    }
};


UI.resources = {};
UI.resources.config = {
    attr: {
        id: {
            prev: 'prev',
            curr: 'curr',
            next: 'next',
            total: 'total'
        },
        page: 'data-page'
    }
};
UI.resources.html = {
    layout: function(currPage, nextPage, prevPage, totalPages) {
        var attr = UI.resources.config.attr;
        return $($.parseHTML(
            '<div class="pagination-area">' +
                '<div class="btn-group btn-group-xs" role="group" aria-label="statistics-pagination">' +
                    '<button id="' + attr.id.prev + '" type="button" ' + attr.page + '="' + prevPage + '" class="btn btn-default">' +
                        '<span class="glyphicon glyphicon-chevron-left"></span>' +
                    '</button>' +
                    '<button id="' + attr.id.curr + '" type="button" ' + attr.page + '="' + currPage + '" class="btn btn-default" disabled="disabled">' +
                        '<span id="' + attr.id.curr + '">' + currPage + '</span>' +
                        '<span> of </span>' +
                        '<span id="' + attr.id.total + '">' + totalPages + '</span>' +
                    '</button>' +
                    '<button id="' + attr.id.next + '" type="button" ' + attr.page + '="' + nextPage + '" class="btn btn-default">' +
                        '<span class="glyphicon glyphicon-chevron-right"></span>' +
                    '</button>' +
                '</div>' +
            '</div>'
        ));
    }
};
