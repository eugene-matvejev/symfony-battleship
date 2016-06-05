<?php

namespace EM\GameBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 2.0
 */
class GameResultController extends AbstractAPIController
{
    /**
     * @ApiDoc(
     *      section = "Game Result API",
     *      description = "returns game results ordered by date in desc. order",
     *      output = "EM\GameBundle\Response\GameResultsResponse"
     * )
     *
     * @param int $page
     *
     * @return Response
     */
    public function orderedByDateAction(int $page) : Response
    {
        $data = $this->get('battleship_game.service.game_result_model')->prepareResponse($page);

        return $this->prepareSerializedResponse($data);
    }
}
