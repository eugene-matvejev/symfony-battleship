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
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('@App/index.html.twig', []);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function turnAction(Request $request) {
        $player = $request->get('player');
        $x      = $request->get('x');
        $y      = $request->get('y');

        return new JsonResponse(['x' => $x, 'y' => $y, 'player' => $player]);
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

        $model = $this->initModel($request);
        $model->save();
////        $json = json_decode($request->getContent());
        $____ = [];
////        foreach($json as $player) {
////            $____[] = [$player->name, $player->id, $player->data];
////        }

//        $this->getDoctrine()->getManager();
        return new JsonResponse([$____]);
        return new JsonResponse([$request->getContent()]);
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
     * @param Request $request
     * @return BattlefieldModel
     */
    private function initModel(Request $request) {
        return (new BattlefieldModel($request))
                ->setBattlefieldRepository($this->getDoctrine()->getRepository('AppBundle:BattlefieldEntity'))
                ->setCellRepository($this->getDoctrine()->getRepository('AppBundle:CellEntity'))
                ->setCellStateRepository($this->getDoctrine()->getRepository('AppBundle:CellStateEntity'))
                ->setGameRepository($this->getDoctrine()->getRepository('AppBundle:GameEntity'))
                ->setPlayerRepository($this->getDoctrine()->getRepository('AppBundle:PlayerEntity'))
                ->setEntityManager($this->getDoctrine()->getManager());
    }
}
