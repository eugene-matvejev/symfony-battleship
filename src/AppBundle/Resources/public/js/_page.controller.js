$(document).ready(function () {
    var pageMgr = (new PageMgr())
        .loadingMode(true)
        .toggleTitle(document.querySelector('li[data-action="game-new"]'));

    $('.page-sidebar, .page-content')
        .on('click', '.toggle-btn, .switch-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            pageMgr.toggleSidebar();
        });

    $('.page-sidebar')
        .on('click', 'li[data-section], li[data-action]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            pageMgr.switchSection(this);
        });
});

