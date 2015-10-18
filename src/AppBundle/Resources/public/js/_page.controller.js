$(document).ready(function () {
    var pageMgr = new PageMgr();
    $('.toggle-btn, .switch-btn').click(function(e) {
        e.preventDefault();
       pageMgr.toggleSidebar();
    });
});

