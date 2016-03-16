<?php

namespace EM\GameBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 2.0
 */
class GameResultController extends AbstractAPIController
{
    public function orderedByDateAction(Request $request, int $page) : Response
    {
        $data = $this->get('battleship.game.services.game.result.model')->prepareResponse($page);

        return $this->prepareSerializedOutput($request, $data);
    }
}
