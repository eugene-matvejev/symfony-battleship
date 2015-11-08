function CellContainer() {
    this.navX = [];
    this.navY = [];
    this.data = [];
}

CellContainer.prototype = {
};

CellContainer.resources = {};
CellContainer.resources.html = {
    layout: function() {
        return $($.parseHTML('<div class="row battlefield-cell-container"></div>'))
    }
};