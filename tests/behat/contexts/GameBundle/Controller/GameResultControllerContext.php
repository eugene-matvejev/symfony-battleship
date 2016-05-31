<?php

namespace EM\Tests\Behat\GameBundle\Controller;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use EM\GameBundle\Controller\GameResultController;
use EM\Tests\Behat\AbstractContainerAwareContext;

/**
 * @see GameResultController
 */
class GameResultControllerContext extends AbstractContainerAwareContext implements Context, SnippetAcceptingContext
{
    /**
     * @Then observe :expectedAmount results in page :page
     *
     * @param int $page
     * @param int $expectedAmount
     */
    public function observeResultsInPage(int $page, int $expectedAmount)
    {
        $response = json_decode(self::$_client->getResponse()->getContent());

        $this->assertInstanceOf(\stdClass::class, $response->meta);
        $this->assertEquals($page, $response->meta->currentPage);
        $this->assertCount($expectedAmount, $response->results);
    }

    /**
     * @Then observe JSON successful response
     */
    public function observeJsonSuccessfulResponse()
    {
        $this->assertSuccessfulJSONResponse(self::$_client->getResponse());
    }
}
