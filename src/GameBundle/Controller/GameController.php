<?php

namespace GameBundle\Controller;

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
        $model = $this->get('battleship.game.services.game.model');
        $json  = $model->init($request->getContent());

        return new JsonResponse($json);
    }

    public function turnAction(Request $request) : JsonResponse
    {
        $model = $this->get('battleship.game.services.game.model');
        $json  = $model->nextTurn($request->getContent());

        return new JsonResponse($json);
    }
}