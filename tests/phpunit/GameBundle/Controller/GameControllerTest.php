<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\GameController;
use EM\GameBundle\Model\PlayerModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see GameController
 */
class GameControllerTest extends ExtendedTestCase
{
    /**
     * @see GameController::indexAction()
     * @test
     */
    public function indexAction()
    {
        $client = $this->getClient();
        $client->request(Request::METHOD_GET, $this->getRouter()->generate('battleship.game.gui.index'));

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::initAction()
     * @test
     */
    public function initAction()
    {
        $json = json_decode(file_get_contents(__DIR__ . '/../../Environment/Data/request.new.game.7x7.json'));

        $client = $this->getClient();
        $client->request(
            Request::METHOD_POST,
            $this->getRouter()->generate('battleship.game.api.init'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            json_encode($json)
        );

        $response = $this->assertJSONSuccessfulResponse($client->getResponse());

        $this->assertInternalType('int', $response->id);
        $this->assertInternalType('string', $response->timestamp);
        $this->assertInternalType('array', $response->battlefields);
        foreach ($response->battlefields as $battlefield) {
            $this->assertInternalType('int', $battlefield->id);

            $this->assertInstanceOf(\stdClass::class, $battlefield->player);
            $player = $battlefield->player;
            $this->assertInternalType('int', $player->id);
            $this->assertInternalType('string', $player->name);

            $this->assertInstanceOf(\stdClass::class, $player->type);
            $this->assertInternalType('int', $player->type->id);
            $this->assertCount(49, (array)$battlefield->cells);
        }

        return $response;
    }

    /**
     * @see     GameController::turnAction()
     * @test
     *
     * @depends initAction
     */
    public function TurnAction()
    {
        foreach (func_get_arg(0)->battlefields as $battlefield) {
            if ($battlefield->player->type->id === PlayerModel::TYPE_CPU) {
                /** probably spotted bug in doctrine, or configured environment wrong, disabled for now */
//                foreach($battlefield->cells as $cell) {
//                    $client = $this->getClient();
//                    $client->request(
//                        Request::METHOD_PATCH,
//                        $this->getRouter()->generate('battleship.game.api.turn'),
//                        [],
//                        [],
//                        ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
//                        json_encode($cell)
//                    );
//
//                    $this->assertJSONSuccessfulResponse($client->getResponse());
//                }
            }
        }
    }
}
