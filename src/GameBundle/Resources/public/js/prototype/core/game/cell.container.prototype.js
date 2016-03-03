'use strict';

/**
 * @constructor
 */
function CellContainer() {
    this.navX = [];
    this.navY = [];
    this.data = [];
    let asd = this.date.map(el => el.value === '');
    let asd1;
    for(let i = 0;;) {
        asd1.push()
    }


}

CellContainer.prototype = {
    getJSON: function() {
        return this.data;
    }
};

CellContainer.resources = {};
CellContainer.resources.html = {
    /**
     * @returns {string}
     */
    layout: function () {
        return '<div class="row battlefield-cell-container"></div>';
    }
};
