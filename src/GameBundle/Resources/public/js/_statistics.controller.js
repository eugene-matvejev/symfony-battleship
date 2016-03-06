$(document).ready(function() {
    var statistics = new Statistics();

    $('.page-sidebar')
        .on('click', 'li[data-section="stats-area"]', function(e) {
            e.stopPropagation();

            statistics.pageMgr.switchSection(this);
            statistics.fetch(1);
        });

    $('div#stats-area')
        .on('click', 'button[data-page]', function(e) {
            e.stopPropagation();

            statistics.fetch(parseInt(this.getAttribute('data-page')));
        });
});
