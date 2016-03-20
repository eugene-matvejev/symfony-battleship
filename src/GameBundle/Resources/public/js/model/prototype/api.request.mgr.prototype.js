'use strict';

/**
 * @constructor
 */
function APIRequestMgr() {
    this.pageMgr = new PageMgr();
}

/**
 * @type {PageMgr} pageMgr
 */
APIRequestMgr.prototype = {
    /**
     * @param {string}        requestMethod
     * @param {string}        requestURL
     * @param {string|Object} requestData
     * @param {function}      onSuccess
     * @param {function}      [onError]
     */
    request: function (requestMethod, requestURL, requestData, onSuccess, onError) {
        let self = this;

        if (requestData instanceof Object) {
            requestData = JSON.stringify(requestData);
        }

        $.ajax({
            contentType: 'application/json; charset=utf-8',
            accepts: 'application/json',
            dataType: 'json',
            method: requestMethod,
            url: requestURL,
            data: requestData,
            timeout: APIRequestMgr.resources.config.timeout,
            beforeSend: function () {
                self.pageMgr.loadingMode(true);
                console.log(' >>> ' + requestMethod + ' :: ' + requestURL, requestData);
            },
            complete: function () {
                self.pageMgr.loadingMode(false);
            },
            success: onSuccess,
            error: onError
        });
    }
};

APIRequestMgr.resources = {};
APIRequestMgr.resources.config = {
    /** @type {int} */
    timeout: 2000 /** in milliseconds */
};
