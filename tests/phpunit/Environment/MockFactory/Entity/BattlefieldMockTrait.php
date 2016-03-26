<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Battlefield;

/**
 * @since 7.0
 */
trait BattlefieldMockTrait
{
    use CellMockTrait, PlayerMockTrait;

    protected function getBattlefieldMock() : Battlefield
    {
        $battlefield = (new Battlefield())
            ->setPlayer($this->getPlayerMock('TEST PLAYER'));

        /** cell's coordinate pattern: /[A-Z][1-9][0-9]/ */
        for ($x = 1, $letter = 'A'; $x < 11; $letter++, $x++) {
            for ($digit = 1; $digit < 11; $digit++) {
                $battlefield->addCell($this->getCellMock($letter.$digit));
            }
        }

        return $battlefield;
    }
}
