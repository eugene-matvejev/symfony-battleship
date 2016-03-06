'use strict';

/**
 * @constructor
 */
function APIMgr() {
    this.pageMgr = new PageMgr();
}

APIMgr.prototype = {
    /**
     * @param {string}             requestMethod
     * @param {string}             requestURL
     * @param {string|Object}      requestData
     * @param {function}           onSuccess
     * @param {function|undefined} onError
     */
    request: function(requestMethod, requestURL, requestData, onSuccess, onError) {
        let self = this;
        //console.log();
        console.log(requestData);
        if (requestData instanceof Object) {
            requestData = JSON.stringify(requestData);
        }

        $.ajax({
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            method: requestMethod,
            url: requestURL,
            data: requestData,
            beforeSend: function() {
                self.pageMgr.loadingMode(true);
                console.log(' >>> ' + requestMethod + ' ' + requestURL, requestData);
            },
            success: onSuccess,
            error: onError,
            timeout: 1000,
            complete: function(jqXHR, status) {
                self.pageMgr.loadingMode(false);
//                console.log(' <<< ' + requestMethod + ' ' + requestURL + ' : ' + status, jqXHR.responseText);
            }
        });
    }
};
