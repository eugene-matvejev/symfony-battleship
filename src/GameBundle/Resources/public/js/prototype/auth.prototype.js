'use strict';

class Auth {
    constructor() {
    }

    setToken(token) {
        this.token = token;
    }

}

Auth.resources.html = {
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
                        <h4 class="modal-title">sign up</h4> \
                    </div> \
                    <div class="modal-body"> \
                        <div class="form-group"> \
                            <label class="control-label" for="model-input-player-name">your name</label> \
                            <input type="text" class="form-control" id="model-input-player-name" placeholder=""> \
                            <span class="help-block">name.surname@gmail.com</span>
                        </div> \
                        <div class="form-group"> \
                            <label class="control-label" for="model-input-battlefield-size">battlefield size</label> \
                            <input type="test" class="form-control" id="model-input-battlefield-size" placeholder="password"/> \
                            <span class="help-block">pA55word</span>
                        </div> \
                    </div> \
                    <div class="modal-footer"> \
                        <button type="button" id="model-button-init-new-game" class="btn btn-primary" disabled="disabled">register</button> \
                    </div> \
                </div> \
            </div> \
        </div>`
};
