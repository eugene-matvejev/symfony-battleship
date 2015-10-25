function APIMgr() {

}

APIMgr.prototype = {
    fetch: function(requestURL, requestMethod, requestData, onSuccess, onError) {
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