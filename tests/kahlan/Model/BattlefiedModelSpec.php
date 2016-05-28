<?php
namespace EM\Tests\Kahlan\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\Tests\Environment\MockFactory;

/**
 * @see BattlefieldModel
 */
describe(BattlefieldModel::class, function () {
    /**
     * @see BattlefieldModel::getLiveCells
     */
    describe('::getLiveCells - should return array of cells which are not flagged with CellModel::FLAG_DEAD', function () {
        before(function () {
            $this->iterateRecievedCells = function (array $cells) {
                foreach ($cells as $index => $cell) {
                    expect($index)->toBeA('int');
                    expect($cell)->toBeAnInstanceOf(Cell::class);
                    expect($cell->hasFlag(CellModel::FLAG_DEAD))->toBe(false);
                }
            };
        });
        beforeEach(function () {
            $this->battlefield = MockFactory::getBattlefieldMock();
        });
        it('should return 49 cells from 7x7 battlefield as all of them not flagged with CellModel::FLAG_DEAD', function () {
//            $battlefield = MockFactory::getBattlefieldMock();
            $cells = BattlefieldModel::getLiveCells($this->battlefield);
            expect($cells)
                ->toBeA('array')
                ->toHaveLength(49);

            $this->iterateRecievedCells($cells);
        });
        it('should return 48 cells from 7x7 battlefield 48 of them not flagged with CellModel::FLAG_DEAD', function () {
//            $battlefield = MockFactory::getBattlefieldMock();
            $this->battlefield->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_DEAD);

            $cells = BattlefieldModel::getLiveCells($this->battlefield);
            expect($cells)
                ->toBeA('array')
                ->toHaveLength(48);

            $this->iterateRecievedCells($cells);
        });
        it('should return empty array from 7x7 battlefield all of them flagged with CellModel::FLAG_DEAD', function () {
//            $battlefield = MockFactory::getBattlefieldMock();
            foreach ($this->battlefield->getCells() as $cell) {
                $cell->addFlag(CellModel::FLAG_DEAD);
            }

            $cells = BattlefieldModel::getLiveCells($this->battlefield);
            expect($cells)
                ->toBeA('array')
                ->toHaveLength(0);
        });
    });
    /**
     * @see BattlefieldModel::hasUnfinishedShips
     */
    describe('::hasUnfinishedShips - should return true if Battlefield contains cells flagged with CellModel::FLAG_SHIP and not flagged with CellModel::FLAG_DEAD', function () {
        it('should return false', function () {
            $battlefield = MockFactory::getBattlefieldMock();

            expect(BattlefieldModel::hasUnfinishedShips($battlefield))->toBe(false);
        });
        it('should return true', function () {
            $battlefield = MockFactory::getBattlefieldMock();
            $battlefield->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_SHIP);

            expect(BattlefieldModel::hasUnfinishedShips($battlefield))->toBe(true);
        });
    });
});
