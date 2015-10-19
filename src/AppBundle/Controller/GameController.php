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
    public function turnAction(Request $request) {
        $model = $this->initModel(new \stdClass());

        return new JsonResponse($model->nextTurn(json_decode($request->getContent())));
    }


    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function startAction(Request $request)
    {
        $model = $this->initModel();

        return new JsonResponse($model->save(json_decode($request->getContent())));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function finishAction(Request $request)
    {
        return new JsonResponse($request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        return new JsonResponse($request);
    }

    /**
     * @return GameModel
     */
    private function initModel() {
        return (new GameModel($this->getDoctrine()->getRepository('AppBundle:CellState')->getStates()))
            ->setBattlefieldRepository($this->getDoctrine()->getRepository('AppBundle:Battlefield'))
            ->setCellRepository($this->getDoctrine()->getRepository('AppBundle:Cell'))
            ->setGameRepository($this->getDoctrine()->getRepository('AppBundle:Game'))
            ->setGameResultRepository($this->getDoctrine()->getRepository('AppBundle:GameResult'))
            ->setPlayerRepository($this->getDoctrine()->getRepository('AppBundle:Player'))
            ->setPlayerTypeRepository($this->getDoctrine()->getRepository('AppBundle:PlayerType'))
            ->setEntityManager($this->getDoctrine()->getManager());
    }
}
