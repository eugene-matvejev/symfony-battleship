function CellContainer() {
    this.navX = [];
    this.navY = [];
    this.data = [];
}

CellContainer.prototype = {
};

CellContainer.getHTML = function() {
    return $($.parseHTML('<div class="row battlefield-cell-container"></div>'));
};