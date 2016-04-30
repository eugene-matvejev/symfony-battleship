'use strict';

class APIRequestMgr {
    constructor() {
        this.pageMgr = new PageMgr();
    }

    /**
     * @param {string}        requestMethod
     * @param {string}        requestURL
     * @param {string|Object} [requestData]
     * @param {function}      [onSuccess]
     * @param {function}      [onError]
     */
    request(requestMethod, requestURL, requestData, onSuccess, onError) {
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
            timeout: APIRequestMgr.resources.config.defaultTimeout,
            beforeSend: function () {
                self.pageMgr.loadingMode(true);
                console.log(` >>> ${requestMethod} :: ${requestURL}`, requestData);
            },
            complete: function (jqXHR) {
                self.pageMgr.loadingMode(false);
                console.log(` >>> ${requestMethod} :: ${requestURL}`, jqXHR);
            },
            success: onSuccess,
            error: onError
        });
    }
}

APIRequestMgr.resources = {
    config: {
        /** @type {number} */
        defaultTimeout: 5000 /** in milliseconds */
    }
};
