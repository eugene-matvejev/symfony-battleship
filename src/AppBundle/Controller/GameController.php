<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GameController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@App/index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function initAction(Request $request)
    {
        $model = $this->get('battleship.game.services.game.model');
        $json  = $model->init($request->getContent());

        return new JsonResponse($json);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function turnAction(Request $request)
    {
        $model = $this->get('battleship.game.services.game.model');
        $json  = $model->nextTurn($request->getContent());

        return new JsonResponse($json);
    }
}
