<?php

namespace AppBundle\Controller;

use AppBundle\Model\BattlefieldModel;
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
     * @return BattlefieldModel
     */
    private function initModel() {
        return (new BattlefieldModel($this->getDoctrine()->getRepository('AppBundle:CellState')->getStates()))
                ->setPlayerRepository($this->getDoctrine()->getRepository('AppBundle:Player'))
                ->setPlayerTypeRepository($this->getDoctrine()->getRepository('AppBundle:PlayerType'))
                ->setGameRepository($this->getDoctrine()->getRepository('AppBundle:Game'))
                ->setBattlefieldRepository($this->getDoctrine()->getRepository('AppBundle:Battlefield'))
                ->setCellRepository($this->getDoctrine()->getRepository('AppBundle:Cell'))
                ->setEntityManager($this->getDoctrine()->getManager());
    }
}
