<?php

namespace EM\Tests\PHPUnit\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\Entity\GameResult;
use EM\Tests\Environment\ContainerAwareTestSuite;
use EM\Tests\Environment\MockFactory\Entity\GameResultMockTrait;

/**
 * @see GameResult
 */
class GameResultTest extends ContainerAwareTestSuite
{
    use GameResultMockTrait;

    /**
     * @see GameResult::setTimestamp
     * @test
     */
    public function setTimestampSetOnPersist()
    {
        $result = $this->getGameResultMock(2, 0);
        $player = $result->getGame()->getBattlefields()[0]->getPlayer();
        $result->setPlayer($player);
        static::$om->persist($result->getGame());

        $this->assertInstanceOf(\DateTime::class, $result->getTimestamp());

        /** clear persisted record */
        static::$om->clear();
    }
}
