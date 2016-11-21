<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\DataFixtures\ORM\LoadPlayerData;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\Player;
use EM\Tests\Environment\AbstractControllerTestCase;
use EM\Tests\Environment\Cleaner\CellModelCleaner;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see GameController
 */
class GameControllerTest extends AbstractControllerTestCase
{
    public function initActionProvider() : array
    {
        $suites = [];
        $finder = new Finder();
        $finder->files()->in("{$this->getSharedFixturesDirectory()}/game-initiation-requests");

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $suites[$file->getFilename()] = [
                $file->getRelativePath() === 'invalid' ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED,
                $file->getContents()
            ];
        }

        return $suites;
    }

    /**
     * @see          GameController::initAction
     * @test
     *
     * @dataProvider initActionProvider
     *
     * @param int    $expectedStatusCode
     * @param string $content
     */
    public function initAction(int $expectedStatusCode, string $content)
    {
        $client = $this->getAuthorizedClient(LoadPlayerData::TEST_PLAYER_EMAIL);
        $client->request(
            Request::METHOD_POST,
            '/api/game-init',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            $content
        );

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
    }

    public function turnActionCoordinatesProvider() : array
    {
        return [
//            'not existed cell'          => [Response::HTTP_NOT_FOUND, 'A0'],
            'first request on A1 cell'  => [Response::HTTP_OK, 'A1'],
            'second request on A1 cell' => [Response::HTTP_UNPROCESSABLE_ENTITY, 'A1'],
            //'first request on A2 cell'  => [Response::HTTP_OK, 'A2'],
            //'second request on A2 cell' => [Response::HTTP_UNPROCESSABLE_ENTITY, 'A2'],
            //'first request on A3 cell'  => [Response::HTTP_OK, 'A3'],
            //'second request on A3 cell' => [Response::HTTP_UNPROCESSABLE_ENTITY, 'A3'],
            //'first request on A4 cell'  => [Response::HTTP_OK, 'A4'],
            //'second request on A4 cell' => [Response::HTTP_UNPROCESSABLE_ENTITY, 'A4'],
            //'first request on A5 cell'  => [Response::HTTP_OK, 'A5'],
            //'second request on A5 cell' => [Response::HTTP_UNPROCESSABLE_ENTITY, 'A5'],
            //'first request on A6 cell'  => [Response::HTTP_OK, 'A6'],
            //'second request on A6 cell' => [Response::HTTP_UNPROCESSABLE_ENTITY, 'A6'],
            //'first request on A7 cell'  => [Response::HTTP_OK, 'A7'],
            //'second request on A7 cell' => [Response::HTTP_UNPROCESSABLE_ENTITY, 'A7']
        ];
    }

    /**
     * @see          GameController::turnAction
     * @test
     *
     * @dataProvider turnActionCoordinatesProvider
     */
    public function unsuccessfulTurnActionOnNotExistingCell()
    {
        $client = $this->getAuthorizedClient(LoadPlayerData::TEST_PLAYER_EMAIL);
        $client->request(
            Request::METHOD_PATCH,
            '/api/game-turn/cell-id/0',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );
        $this->assertUnsuccessfulResponse($client->getResponse());
    }

    /**
     * simulate human interaction until game has been finished
     *
     * @see          GameController::turnAction
     * @test
     *
     * @dataProvider turnActionCoordinatesProvider
     *
     * @param int    $expectedStatusCode
     * @param string $coordinate
     */
    public function turnAction(int $expectedStatusCode, string $coordinate)
    {
        CellModelCleaner::resetChangedCells();

        $game        = static::$om->getRepository(Game::class)->findBy([], ['id' => 'ASC'])[0];
        $player      = static::$om->getRepository(Player::class)->findOneBy(['email' => 'CPU 0']);
        $battlefield = static::$om->getRepository(Battlefield::class)->findOneBy(['player' => $player, 'game' => $game]);

        $cell = $battlefield->getCellByCoordinate($coordinate);

        $client = $this->getAuthorizedClient(LoadPlayerData::TEST_PLAYER_EMAIL);
        $client->request(
            Request::METHOD_PATCH,
            "/api/game-turn/cell-id/{$cell->getId()}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
    }
}
