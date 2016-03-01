function APIMgr() {
    this.pageMgr = new PageMgr();
}

APIMgr.prototype = {
    request: function(requestMethod, requestURL, requestData, onSuccess, onError, onComplete) {
        console.log(requestMethod, requestURL, requestData);
        var self = this;
        if(onError === undefined) {
            onError = function() {
                self.pageMgr.loadingMode(false);
            };
        }
        if(onComplete === undefined) {
            onComplete = function(json) {
                console.log(' >>> ' + requestMethod, requestURL, json, json.responseText);
            };
        }

        $.ajax({
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            method: requestMethod,
            url: requestURL,
            data: requestData,
            success: onSuccess,
            complete: onComplete,
            error: onError
        });
    }
};
