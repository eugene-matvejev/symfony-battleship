function APIMgr() {
    this.pageMgr   = new PageMgr();
}

APIMgr.prototype = {
    request: function(requestMethod, requestURL, requestData, onSuccess, onError) {
        console.log(requestMethod, requestURL, requestData);
        var self = this;
        if(onError === undefined) {
            onError = function(json) {
                self.pageMgr.loadingMode(false);
            };
        }

        $.ajax({
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            method: requestMethod,
            url: requestURL,
            data: requestData,
            success: onSuccess,
            error: onError
        });
    }
};
