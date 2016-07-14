'use strict';

class APIRequestService {
    constructor() {
        this.pageMgr = new PageMgr();
    }

    /**
     * @param {string}   requestMethod
     * @param {string}   requestURL
     * @param {Object}   [requestData]
     * @param {function} [onSuccess]
     * @param {function} [onError]
     */
    request(requestMethod, requestURL, requestData, onSuccess, onError) {
        let self    = this;
        requestData = JSON.stringify(requestData);

        $.ajax({
            contentType: 'application/json; charset=utf-8',
            accepts: 'application/json',
            dataType: 'json',
            headers: {
                'x-wsse': 'asd'
            },
            method: requestMethod,
            url: requestURL,
            data: requestData,
            timeout: APIRequestService.resources.config.defaultTimeout,
            beforeSend: function () {
                self.pageMgr.loadingMode(true);
                console.log(` >>> ${requestMethod} :: ${requestURL}`, requestData || '');
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

APIRequestService.resources = {
    config: {
        /** @type {number} */
        defaultTimeout: 5000 /** in milliseconds */
    }
};
