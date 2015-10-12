<?php

namespace AppBundle\Controller;

use AppBundle\Model\BattlefieldModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
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
        $json = json_decode($request->getContent());
        $model = $this->initModel(new \stdClass());

        return new JsonResponse([
            $model->PlayerTurn($json),
            $model->AIturn($json)
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request) {

        return new JsonResponse($request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function startAction(Request $request) {

        $model = $this->initModel(json_decode($request->getContent()));
        $model->save();
        return new JsonResponse($model->getJSON());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function finishAction(Request $request) {

        return new JsonResponse($request);
    }

    /**
     * @param \stdClass $json
     *
     * @return BattlefieldModel
     */
    private function initModel(\stdClass $json) {
        return (new BattlefieldModel($json, $this->getDoctrine()->getRepository('AppBundle:CellStateEntity')->getStates()))
                ->setPlayerRepository($this->getDoctrine()->getRepository('AppBundle:PlayerEntity'))
                ->setGameRepository($this->getDoctrine()->getRepository('AppBundle:GameEntity'))
                ->setBattlefieldRepository($this->getDoctrine()->getRepository('AppBundle:BattlefieldEntity'))
                ->setCellRepository($this->getDoctrine()->getRepository('AppBundle:CellEntity'))
                ->setEntityManager($this->getDoctrine()->getManager());
    }
}
