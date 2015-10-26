function APIMgr() {
    this.debugArea = document.querySelector('#debug-area');
    this.debugText = document.querySelector('#debug-area>div.debug-content');
    this.pageMgr   = new PageMgr();
}

APIMgr.prototype = {
    request: function(requestURL, requestMethod, requestData, onSuccess, onError) {
        //debugger;
        if(onError === undefined) {
            var self = this;
            onError = function(json) {
                console.log(json);
                self.pageMgr.loadingMode(false);
                self.debugText.innerHTML = json.responseText;
            };
        }

        $.ajax({
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            method: requestMethod,
            url: requestURL,
            data: requestData,
            success: onSuccess,
            error: onError
        });
    }
};