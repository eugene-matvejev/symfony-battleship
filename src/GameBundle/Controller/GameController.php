<?php

namespace EM\GameBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 1.0
 */
class GameController extends AbstractAPIController
{
    public function indexAction() : Response
    {
        return $this->render('@Game/index.html.twig');
    }

    public function initAction(Request $request) : Response
    {
        if (empty($request->getContent())) {
            return new JsonResponse();
        }

        $game = $this->get('battleship.game.services.game.model')->init($request->getContent());

        return $this->prepareSerializedOutput($game, Response::HTTP_CREATED);
    }

    public function turnAction(Request $request) : Response
    {
        if (empty($request->getContent())) {
            return new JsonResponse();
        }

        $data = $this->get('battleship.game.services.game.model')->nextTurn($request->getContent());

        return $this->prepareSerializedOutput($data);
    }
}
