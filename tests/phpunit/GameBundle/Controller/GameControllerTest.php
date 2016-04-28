<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\GameController;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use EM\Tests\Environment\ContainerAwareTestSuite;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see GameController
 */
class GameControllerTest extends ContainerAwareTestSuite
{
    /**
     * @see GameController::indexAction
     * @test
     */
    public function indexAction()
    {
        $client = clone static::$client;
        $client->request(
            Request::METHOD_GET,
            static::$router->generate('battleship.game.gui.index')
        );
        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::initAction
     * @test
     */
    public function unsuccessfulInitAction()
    {
        foreach (['application/xml', 'application/json'] as $acceptHeader) {
            $client = clone static::$client;
            $client->request(
                Request::METHOD_POST,
                static::$router->generate('battleship.game.api.init'),
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => $acceptHeader]
            );
            $this->assertUnsuccessfulResponse($client->getResponse());
        }
    }

    /**
     * @see     GameController::initAction
     * @test
     *
     * @depends unsuccessfulInitAction
     */
    public function successfulInitAction_JSON()
    {
        $json = json_decode(file_get_contents(__DIR__ . '/../../../data/new_game_request_7x7_2_players.json'));

        $client = clone static::$client;
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('battleship.game.api.init'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            json_encode($json)
        );
        $this->assertSuccessfulJSONResponse($client->getResponse());

        $response = json_decode($client->getResponse()->getContent());
        $this->assertInternalType('int', $response->id);
        $this->assertInternalType('string', $response->timestamp);
        $this->assertInternalType('array', $response->battlefields);
        foreach ($response->battlefields as $battlefield) {
            $this->assertInternalType('int', $battlefield->id);
            $this->assertInstanceOf(\stdClass::class, $battlefield->player);

            $this->assertInternalType('int', $battlefield->player->id);
            $this->assertInternalType('int', $battlefield->player->flags);
            $this->assertInternalType('string', $battlefield->player->name);

            $this->assertCount(49, (array)$battlefield->cells);
            foreach ($battlefield->cells as $coordinate => $cell) {
                $this->assertInternalType('string', $coordinate);

                $this->assertInternalType('int', $cell->id);
                $this->assertInternalType('int', $cell->flags);
                $this->assertInternalType('string', $cell->coordinate);

                /** as CPU fields should have CellModel::FLAG_NONE on initiation */
                $expected = $battlefield->player->flags == PlayerModel::FLAG_AI_CONTROLLED ? CellModel::FLAG_NONE : $cell->flags;
                $this->assertEquals($expected, $cell->flags);
            }
        }

        /** pass the response to the dependant class */
        return $response;
    }

    /**
     * @see     GameController::initAction
     * @test
     *
     * @depends unsuccessfulInitAction
     */
    public function successfulInitAction_XML()
    {
        $client = clone static::$client;

        $json = json_decode(file_get_contents(__DIR__ . '/../../../data/new_game_request_7x7_2_players.json'));
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('battleship.game.api.init'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/xml'],
            json_encode($json)
        );
        $this->assertSuccessfulXMLResponse($client->getResponse());

        $response = simplexml_load_string($client->getResponse()->getContent(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $response = json_decode(json_encode($response));

        $this->assertInternalType('string', $response->id);
        $this->assertInternalType('string', $response->timestamp);
        $this->assertInternalType('array', $response->battlefields->battlefield);
        foreach ($response->battlefields->battlefield as $battlefield) {
            $this->assertInternalType('string', $battlefield->id);
            $this->assertInstanceOf(\stdClass::class, $battlefield->player);

            $this->assertInternalType('string', $battlefield->player->id);
            $this->assertInternalType('string', $battlefield->player->flags);
            $this->assertInternalType('string', $battlefield->player->name);

            $this->assertCount(49, $battlefield->cells->cell);
            foreach ($battlefield->cells->cell as $cell) {
                $this->assertInternalType('string', $cell->id);
                $this->assertInternalType('string', $cell->flags);
                $this->assertInternalType('string', $cell->coordinate);

                /** as CPU fields should have CellModel::FLAG_NONE on initiation */
                $expected = $battlefield->player->flags == PlayerModel::FLAG_AI_CONTROLLED ? CellModel::FLAG_NONE : $cell->flags;
                $this->assertEquals($expected, $cell->flags);
            }
        }
    }

    /**
     * @see     GameController::turnAction
     * @test
     *
     * @depends successfulInitAction_JSON
     * @depends successfulInitAction_XML
     */
    public function unsuccessfulTurnAction()
    {
        $client = clone static::$client;
        foreach (['application/xml', 'application/json'] as $acceptHeader) {
            $client->request(
                Request::METHOD_PATCH,
                static::$router->generate('battleship.game.api.turn', ['cellId' => 0]),
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => $acceptHeader]
            );
            $this->assertUnsuccessfulResponse($client->getResponse());
        }
    }

    /**
     * simulate human interaction until game has been won
     *
     * @var     \stdClass $response
     *
     * @see     GameController::turnAction
     * @test
     *
     * @depends successfulInitAction_JSON
     */
    public function successfulTurnAction(\stdClass $passedGameResponse)
    {
        foreach ($passedGameResponse->battlefields as $battlefield) {
            if ($battlefield->player->flags === PlayerModel::FLAG_AI_CONTROLLED) {
                foreach ($battlefield->cells as $cell) {
                    $client = clone static::$client;
                    $client->request(
                        Request::METHOD_PATCH,
                        static::$router->generate('battleship.game.api.turn', ['cellId' => $cell->id]),
                        [],
                        [],
                        ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
                    );
                    $this->assertSuccessfulJSONResponse($client->getResponse());

                    $parsed = json_decode($client->getResponse()->getContent());
                    if (isset($parsed->result)) {
                        return;
                    }
                }
            }
        }
    }
}
