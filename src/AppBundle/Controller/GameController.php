<?php

namespace AppBundle\Controller;

use AppBundle\Model\GameModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('@App/index.html.twig', []);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function startAction(Request $request)
    {
        $model = $this->get('battleship.game.services.game.model');
        $json  = $model->init($request->getContent());

        return new JsonResponse($json);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function turnAction(Request $request)
    {
        $model = $this->get('battleship.game.services.game.model');
        $json  = $model->nextTurn($request->getContent());

        return new JsonResponse($json);
    }
}
