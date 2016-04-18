<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Battlefield;

/**
 * @since 7.0
 */
trait BattlefieldMockTrait
{
    use CellMockTrait, PlayerMockTrait;

    protected function getBattlefieldMock(int $size = 7) : Battlefield
    {
        $battlefield = (new Battlefield())
            ->setPlayer($this->getPlayerMock(''));

        /** cell's coordinate pattern: /[A-Z][1-9][0-9]/ */
        for ($x = 0, $letter = 'A'; $x < $size; $letter++, $x++) {
            for ($digit = 0; $digit < $size;) {
                $cell = $this->getCellMock($letter . (++$digit));
                $battlefield->addCell($cell);
            }
        }

        return $battlefield;
    }
}
