<?php

namespace EM\GameBundle\Controller;

use EM\FoundationBundle\Controller\AbstractAPIController;
use EM\Tests\PHPUnit\GameBundle\Controller\GameResultControllerTest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see   GameResultControllerTest
 *
 * @since 2.0
 */
class GameResultController extends AbstractAPIController
{
    /**
     * @see GameResultControllerTest::orderedByDateAction
     *
     * @ApiDoc(
     *      section = "Game:: Results",
     *      description = "returns game results ordered by date in desc. order",
     *      output = "EM\GameBundle\Response\GameResultsResponse"
     * )
     *
     * @Security("has_role('PLAYER')")
     *
     * @param int $page
     *
     * @return Response
     */
    public function orderedByDateAction(int $page) : Response
    {
        $data = $this->get('em.game_bundle.service.game_result_model')->buildResponse($page);

        return $this->prepareSerializedResponse($data);
    }
}
