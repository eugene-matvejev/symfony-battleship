function BattlefieldData() {

}
BattlefieldData.prototype = {
    navX:     [],
    navY:     [],
    cellData: [],
    cellState: {
        waterLive: 1,
        waterDied: 2,
        shipLive: 3,
        shipDied: 4
    }
};