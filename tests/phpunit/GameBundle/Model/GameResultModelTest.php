<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\GameResult;
use EM\GameBundle\Model\GameResultModel;
use EM\GameBundle\Response\GameResultsResponse;
use EM\Tests\Environment\AbstractKernelTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see GameResultModel
 */
class GameResultModelTest extends AbstractKernelTestSuite
{
    /**
     * @var GameResultModel
     */
    private static $gameResultModel;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$gameResultModel = static::$container->get('em.game_bundle.service.game_result_model');
    }

    /**
     * @see GameResultModel::prepareResponse
     * @test
     */
    public function prepareResponse()
    {
        $perPage = static::$container->getParameter('em.game_bundle.setting.limits.per_page.game_results');

        /** populated 2 full pages of Game Results + 1 result */
        for ($i = 0; $i < $perPage * 2 + 1; $i++) {
            $result = MockFactory::getGameResultMock(2, 0);
            $user = $result->getGame()->getBattlefields()[0]->getUser();
            $result->setUser($user);

            static::$om->persist($result->getGame());
        }
        static::$om->flush();

        /** should be 3 pages in total */
        for ($page = 1; $page < 3; $page++) {
            $response = static::$gameResultModel->buildResponse($page);

            $this->assertEquals($page, $response->getMeta()[GameResultsResponse::META_INDEX_CURRENT_PAGE]);
            $this->assertGreaterThanOrEqual(3, $response->getMeta()[GameResultsResponse::META_INDEX_TOTAL_PAGES]);

            $this->assertInternalType('array', $response->getResults());
            $this->assertContainsOnlyInstancesOf(GameResult::class, $response->getResults());
            $this->assertLessThanOrEqual($perPage, count($response->getResults()));
        }
    }
}
