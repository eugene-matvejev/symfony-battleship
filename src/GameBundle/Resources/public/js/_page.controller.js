$(document).ready(function () {
    var pageMgr = new PageMgr();

    $('.page-sidebar, .page-content')
        .on('click', '.toggle-btn, .switch-btn', function(e) {
            e.stopPropagation();

            pageMgr.toggleSidebar();
        });

    $('.page-sidebar')
        .on('click', 'li[data-section], li[data-action]', function(e) {
            e.stopPropagation();

            pageMgr.switchSection(this);
        })
        .on('click', 'button.button:not(.disabled)', function(e) {
            e.stopPropagation();

            pageMgr.fetch();
        });
});

