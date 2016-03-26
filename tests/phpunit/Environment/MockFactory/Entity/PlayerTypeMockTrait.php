<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory;

use EM\GameBundle\Entity\PlayerType;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 7.0
 */
trait PlayerTypeMockTrait
{
    private function getPlayerTypeMock(int $type = null) : PlayerType
    {
        return (new PlayerType())
            ->setId($type ?? PlayerModel::TYPE_HUMAN);
    }
}
