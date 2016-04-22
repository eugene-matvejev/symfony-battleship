'use strict';

$(document).ready(function () {
    let alertMgr = new AlertMgr();

    $('#notification-area')
        .on('click', 'span.notification-control', function (e) {
            e.stopPropagation();

            alertMgr.hide();
        });
});
