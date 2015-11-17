function APIMgr() {
    this.$debugText = $('#debug-area>div.debug-content');
    this.pageMgr   = new PageMgr();
}

APIMgr.prototype = {
    request: function(requestMethod, requestURL, requestData, onSuccess, onError) {
        var self = this;
        if(onError === undefined) {
            onError = function(json) {
                self.pageMgr.loadingMode(false);
                self.updateDebugHTML(json.responseText, true);
            };
        }

        $.ajax({
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            method: requestMethod,
            beforeSent: self.updateDebugHTML(requestData),
            url: requestURL,
            data: requestData,
            success: onSuccess,
            error: onError
        });
    },
    updateDebugHTML: function(html, extend) {
        extend !== undefined
            ? this.$debugText.html(this.$debugText.html() + html)
            : this.$debugText.html(html);
    }
};
