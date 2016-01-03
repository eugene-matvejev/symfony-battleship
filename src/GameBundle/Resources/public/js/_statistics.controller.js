$(document).ready(function () {
    var statisticsMgr = new Statistics();

    $('.page-sidebar')
        .on('click', 'li[data-section="stats-area"]', function(e) {
            e.stopPropagation();

            statisticsMgr.pageMgr.switchSection(this);
            statisticsMgr.fetch(1);
        });

    $('div#stats-area')
        .on('click', 'button[data-page]', function(e) {
            e.stopPropagation();

            var page = this.getAttribute('data-page');
            //console.log(this, page);
            //statisticsMgr.pageMgr.switchSection(this);
            statisticsMgr.fetch(page);
        });
});