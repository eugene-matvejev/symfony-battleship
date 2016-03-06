<?php

namespace EM\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 1.0
 */
class GameController extends Controller
{
    public function indexAction() : Response
    {
        return $this->render('@Game/index.html.twig');
    }

    public function initAction(Request $request) : JsonResponse
    {
        if (empty($request->getContent())) {
            return new JsonResponse();
        }

        $json = $this->get('battleship.game.services.game.model')->init($request->getContent());

        return new JsonResponse($json, Response::HTTP_CREATED);
    }

    public function turnAction(Request $request) : JsonResponse
    {
        if (empty($request->getContent())) {
            return new JsonResponse();
        }

        $json = $this->get('battleship.game.services.game.model')->nextTurn($request->getContent());

        return new JsonResponse($json);
    }
}
