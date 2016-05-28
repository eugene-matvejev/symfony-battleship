<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Model\GameResultModel;
use EM\GameBundle\Response\GameResultsResponse;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory;

/**
 * @see GameResultModel
 */
class GameResultModelTest extends IntegrationTestSuite
{
    /**
     * @var GameResultModel
     */
    private $gameResultModel;

    protected function setUp()
    {
        $this->gameResultModel = static::$container->get('battleship.game.services.game.result.model');
    }

    /**
     * @see GameResultModel::prepareResponse
     * @test
     */
    public function prepareResponse()
    {
        $perPage = static::$container->getParameter('battleship_game.game_results_per_page');

        /** populated 2 full pages of Game Results + 1 result */
        for ($i = 0; $i < ($perPage * 2 + 1); $i++) {
            $result = MockFactory::getGameResultMock(2, 0);
            $player = $result->getGame()->getBattlefields()[0]->getPlayer();
            $result->setPlayer($player);

            static::$om->persist($result->getGame());
        }
        static::$om->flush();

        /** should be 3 pages in total */
        for ($page = 1; $page < 3; $page++) {
            $response = $this->gameResultModel->prepareResponse($page);

            $this->assertEquals($page, $response->getMeta()[GameResultsResponse::META_INDEX_CURRENT_PAGE]);
            $this->assertEquals(3, $response->getMeta()[GameResultsResponse::META_INDEX_TOTAL_PAGES]);

            $this->assertInternalType('array', $response->getResults());
            $this->assertContainsOnlyInstancesOf(GameResult::class, $response->getResults());
            $this->assertLessThanOrEqual($perPage, count($response->getResults()));
        }
    }
}
