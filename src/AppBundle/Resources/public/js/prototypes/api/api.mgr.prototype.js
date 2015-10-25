function APIMgr() {

}

APIMgr.prototype = {
    request: function(requestURL, requestMethod, requestData, onSuccess, onError) {
        //debugger;
        if(onError === undefined) {
            onError = function(json) {
                $("#debug-area").html(json.responseText);
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