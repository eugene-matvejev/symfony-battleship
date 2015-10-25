function StatisticsMgr() {
    this.pageMgr  = new PageMgr();
    this.alertMgr = new AlertMgr();
    this.$area    = $('#stats-area');
}

StatisticsMgr.prototype = {
    data: undefined,
    fetch: function() {
        $.ajax()
    },
    getHTML: function() {

    }
};

