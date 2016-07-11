'use strict';

class Auth {
    constructor() {
        let token = this.loadToken();
        if (null !== token) {
            this.setToken(token);
        }

        this.modalMgr = new ModalMgr();
        this.modalMgr
            .updateHTML(this.constructor.resources.html.modal)
            .show();
    }

    setToken(token) {
        this.token = token;
    }

    loadToken() {
        sessionStorage.getItem(this.constructor.resources.config.tokenKey)
    }

    saveToken(token) {
        sessionStorage.setItem(this.constructor.resources.config.tokenKey, token);
    }

    parseAuthorizationResponse() {

    }

    //
    // parsePlayerCreatedResponse() {
    //
    // }
}

Auth.resources        = {};
Auth.resources.config = {
    tokenKey: 'token',
    pattern: {
        /** @type {Object} */
        username: /^[a-zA-Z0-9\.\- @]{3,25}$/
    }
};
Auth.resources.html   = {
    /**
     * @returns {string}
     */
    modal: ` \
        <div class="modal fade"> \
            <div class="modal-dialog"> \
                <div class="modal-content"> \
                    <div class="modal-header"> \
                        <button type="button" class="close" data-dismiss="modal"> \
                            <span aria-hidden="true">&times;</span> \
                        </button> \
                        <h4 class="modal-title">
                            <span id="form-title-register" class="hidden">register</span>
                            <span id="form-title-login">login</span>
                        </h4> \
                    </div> \
                    <div class="modal-body"> \
                        <div class="form-group"> \
                            <label class="control-label" for="model-input-username">username</label> \
                            <input type="text" class="form-control" id="model-input-username" placeholder=""> \
                            <span class="help-block">name.surname@gmail.com</span>
                        </div> \
                        <div class="form-group"> \
                            <label class="control-label" for="model-input-password">password</label> \
                            <input type="password" class="form-control" id="model-input-password" placeholder="password"/> \
                            <span class="help-block">pA55word</span> \
                        </div> \
                        <div class="form-group hidden"> \
                            <label class="control-label" for="model-input-password-repeat">repeat password</label> \
                            <input type="password" class="form-control" id="model-input-password-repeat" placeholder="repeat password"/> \
                            <span class="help-block">pA55word</span> \
                        </div> \
                    </div> \
                    <div class="modal-footer"> \
                        <a class="pull-left hidden" href="#form-action-register">already have an account</a>
                        <a class="pull-left" href="#form-action-login">not registered?</a>
                        <button type="button" id="button-auth-form" class="btn btn-primary" disabled="disabled">submit</button> \
                    </div> \
                </div> \
            </div> \
        </div>`
};
