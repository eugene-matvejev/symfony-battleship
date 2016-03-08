'use strict';

/**
 * @constructor
 */
function APIMgr() {
    this.pageMgr = new PageMgr();
}

/**
 * @type {PageMgr} pageMgr
 */
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
            complete: function() {
                self.pageMgr.loadingMode(false);
            }
        });
    }
};
