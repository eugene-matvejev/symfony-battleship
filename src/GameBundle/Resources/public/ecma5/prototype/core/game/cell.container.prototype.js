function CellContainer() {
    this.navX = [];
    this.navY = [];
    this.data = [];
}

CellContainer.prototype = {
    getJSON: function() {
        return this.data;
    }
};

CellContainer.resources = {
    html: {
        layout: function () {
            return $($.parseHTML('<div class="row battlefield-cell-container"></div>'));
        }
    }
};
