$(document).ready(function () {
    var statisticsMgr = new Statistics()

    $('.page-sidebar')
        .on('click', 'li[data-section="stats-area"]', function(e) {
            e.stopPropagation();

            statisticsMgr.pageMgr.switchSection(this);
            statisticsMgr.fetch();
        });
});