<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\GameController;
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
        $client->request(Request::METHOD_GET, static::$router->generate('battleship.game.gui.index'));
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
        $json = json_decode(file_get_contents(__DIR__ . '/../../../data/new.game.2.players.7x7.json.request.json'));

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
            $this->assertInternalType('int', $battlefield->player->flag);
            $this->assertInternalType('string', $battlefield->player->name);

            $this->assertCount(49, (array)$battlefield->cells);
        }
    }

    /**
     * @see     GameController::initAction
     * @test
     *
     * @depends unsuccessfulInitAction
     */
    public function successfulInitAction_XML()
    {
//        $client = clone static::$client;
//
//        $json = json_decode(file_get_contents(__DIR__ . '/../../../data/new.game.2.players.7x7.json.request.json'));
//        $client->request(
//            Request::METHOD_POST,
//            static::$router->generate('battleship.game.api.init'),
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/xml'],
//            json_encode($json)
//        );
//        $arr = static::$om->getUnitOfWork()->getScheduledEntityInsertions();
//
//        $arr;
//        $asd = 'asd';
//
//        $this->assertSuccessfulXMLResponse($client->getResponse());

//        $this->assertInternalType('int', $response->id);
//        $this->assertInternalType('string', $response->timestamp);
//        $this->assertInternalType('array', $response->battlefields);
//        foreach ($response->battlefields as $battlefield) {
//            $this->assertInternalType('int', $battlefield->id);
//
//            $this->assertInstanceOf(\stdClass::class, $battlefield->player);
//            $player = $battlefield->player;
//            $this->assertInternalType('int', $player->id);
//            $this->assertInternalType('string', $player->name);
//
//            $this->assertInstanceOf(\stdClass::class, $player->type);
//            $this->assertInternalType('int', $player->type->id);
//            $this->assertCount(49, (array)$battlefield->cells);
//        }
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
        foreach (['application/xml', 'application/json'] as $acceptHeader) {
            $client = clone static::$client;
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

//    /**
//     * @see     GameController::turnAction
//     * @test
//     *
//     * @depends unsuccessfulTurnAction
//     */
//    public function successfulTurnAction()
//    {
//        $client = clone static::$client;
//        foreach (func_get_arg(0)->battlefields as $battlefield) {
//            if ($battlefield->player->type->id === PlayerModel::TYPE_CPU) {
//                /** probably spotted bug in doctrine, or configured environment wrong, disabled for now */
//                foreach($battlefield->cells as $cell) {
//                    static::$om->clear();
//                    $client->request(
//                        Request::METHOD_PATCH,
//                        $this->getRouter()->generate('battleship.game.api.turn', ['cellId' => $cell->id]),
//                        [],
//                        [],
//                        ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
//                    );
//
//                    $this->assertSuccessfulJSONResponse($client->getResponse());
//                }
//            }
//        }
//    }
}
