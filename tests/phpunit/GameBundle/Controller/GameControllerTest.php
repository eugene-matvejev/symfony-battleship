<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Entity\Battlefield;
use EM\Tests\Environment\Cleaner\CellModelCleaner;
use EM\Tests\Environment\IntegrationTestSuite;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see GameController
 */
class GameControllerTest extends IntegrationTestSuite
{
    public function initActionProvider() : array
    {
        $suites = [];
        $finder = new Finder();
        $finder->files()->in("{$this->getSharedFixturesDirectory()}/game-initiation-requests");

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
        $client = $this->getAuthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('battleship_game.api.init'),
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
            'not existed cell'          => [Response::HTTP_NOT_FOUND, 'A0'],
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
     *
     * @param string $coordinate
     * @param int    $expectedStatusCode
     *
     * @group        asdasd
     */
    public function turnAction(int $expectedStatusCode, string $coordinate)
    {
        CellModelCleaner::resetChangedCells();

        $game        = static::$om->getRepository('GameBundle:Game')->findBy([], ['id' => 'ASC'])[0];
        $player      = static::$om->getRepository('GameBundle:Player')->findOneBy(['email' => 'CPU 0']);
        $battlefield = static::$om->getRepository('GameBundle:Battlefield')->findOneBy(['player' => $player, 'game' => $game]);

        $cell = $battlefield->getCellByCoordinate($coordinate);

        $client = $this->getAuthorizedClient();
        $client->request(
            Request::METHOD_PATCH,
            static::$router->generate('battleship_game.api.turn', ['cellId' => $cell ? $cell->getId() : 0]),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
    }
}
