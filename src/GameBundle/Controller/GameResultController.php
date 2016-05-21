<?php

namespace EM\GameBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * @since 2.0
 */
class GameResultController extends AbstractAPIController
{
    public function orderedByDateAction(int $page) : Response
    {
        $data = $this->get('battleship.game.services.game.result.model')->prepareResponse($page);

        return $this->buildSerializedResponse($data);
    }
}
