function PageMgr() {
    this.$sidebar = $('div.page-sidebar');
    this.$content = $('div.page-content');
}

PageMgr.prototype = {
    toggleSidebar: function() {
        this.$content.toggleClass(PageMgr.toggle);
        this.$sidebar.toggleClass(PageMgr.toggle);
    }
};

PageMgr.toggle = 'toggled';