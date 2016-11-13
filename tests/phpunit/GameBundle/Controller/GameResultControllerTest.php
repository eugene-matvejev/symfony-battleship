<?php

namespace EM\Tests\PHPUnit\GameBundle\Controller;

use EM\GameBundle\Controller\GameResultController;
use EM\Tests\Environment\AbstractControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see GameResultController
 */
class GameResultControllerTest extends AbstractControllerTestCase
{
    public function orderedByDateActionDataProvider() : array
    {
        return [
            [-1,    Response::HTTP_NOT_FOUND],
            ['two', Response::HTTP_NOT_FOUND],
            [0,     Response::HTTP_OK],
            [1,     Response::HTTP_OK],
            [99999, Response::HTTP_OK]
        ];
    }

    /**
     * @see          GameResultController::orderedByDateAction
     * @test
     *
     * @dataProvider orderedByDateActionDataProvider
     *
     * @param int $pageId
     * @param int $expectedResponseCode
     */
    public function orderedByDateAction($pageId, int $expectedResponseCode)
    {
        $client = $this->getAuthorizedClient();
        $client->request(
            Request::METHOD_GET,
            "/api/game-results/page/{$pageId}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );

        $response = $client->getResponse();

        $this->assertEquals($expectedResponseCode, $response->getStatusCode());
    }
}
