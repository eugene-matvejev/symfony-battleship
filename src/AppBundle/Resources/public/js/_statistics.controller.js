$(document).ready(function () {
    var statisticsMgr = new StatisticsMgr();

    $('.page-sidebar')
        .on('click', 'li[data-section="stats-area"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            statisticsMgr.fetch();
        });
});

