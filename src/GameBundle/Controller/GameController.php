<?php

namespace EM\GameBundle\Controller;

use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Exception\PlayerException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Request\GameInitiationRequest;
use EM\GameBundle\Response\GameInitiationResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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

    /**
     * @ApiDoc(
     *      section = "Game API",
     *      description = "Creates a new game from the submitted data",
     *      input = "EM\GameBundle\Request\GameInitiationRequest",
     *      responseMap = {
     *          201 = "EM\GameBundle\Response\GameInitiationResponse"
     *      }
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function initAction(Request $request) : Response
    {
        $_request = new GameInitiationRequest();
        $_request->parse($request->getContent());

        $game = $this->get('battleship.game.services.game.processor')->buildGame($_request);

        $om = $this->getDoctrine()->getManager();
        $om->persist($game);
        $om->flush();

        $response = $this->buildSerializedResponse(
            (new GameInitiationResponse())->setBattlefields($game->getBattlefields()),
            Response::HTTP_CREATED
        );

        return $response;
    }

    /**
     * @ApiDoc(
     *      section = "Game API",
     *      description = "process game turn by cellId",
     *      output = "EM\GameBundle\Response\GameTurnResponse"
     * )
     *
     * @param int $cellId
     *
     * @return Response
     * @throws CellException
     * @throws PlayerException
     */
    public function turnAction(int $cellId) : Response
    {
        if (null === $cell = $this->getDoctrine()->getRepository('GameBundle:Cell')->find($cellId)) {
            throw new CellException("cell: {$cellId} doesn't exist");
        }
        if ($cell->hasFlag(CellModel::FLAG_DEAD)) {
            throw new CellException("cell: {$cellId} doesn't already flagged as *DEAD*");
        }

        $data = $this->get('battleship.game.services.game.processor')->processGameTurn($cell);
        $om = $this->getDoctrine()->getManager();

        foreach (CellModel::getChangedCells() as $cell) {
            $om->persist($cell);
        }
        $om->flush();

        return $this->buildSerializedResponse($data);
    }
}
