<?php

namespace EM\GameBundle\Controller;

use EM\FoundationBundle\Controller\AbstractAPIController;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Exception\GameProcessorException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Request\GameInitiationRequest;
use EM\GameBundle\Response\GameInitiationResponse;
use EM\GameBundle\Response\GameTurnResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * @see   GameControllerTest
 *
 * @since 1.0
 */
class GameController extends AbstractAPIController
{
    /**
     * @ApiDoc(
     *      section = "Game:: Mechanics",
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
        if (!$this->get('battleship_game.validator.game_initiation_request')->validate($request->getContent())) {
            throw new InvalidArgumentException('request validation failed, please check documentation');
        }

        $game = $this->get('battleship_game.service.game_builder')->buildGame(new GameInitiationRequest($request->getContent()));

        $om = $this->getDoctrine()->getManager();
        $om->persist($game);
        $om->flush();

        return $this->prepareSerializedResponse(new GameInitiationResponse($game->getBattlefields()), Response::HTTP_CREATED);
    }

    /**
     * @ApiDoc(
     *      section = "Game:: Mechanics",
     *      description = "process game turn by cellId",
     *      output = "EM\GameBundle\Response\GameTurnResponse",
     *      statusCodes = {
     *          200 = "successful turn",
     *          404 = "cell does not exists",
     *          422 = "cell already flagged as DEAD"
     *      }
     * )
     *
     * @param int $cellId
     *
     * @return Response
     * @throws CellException
     * @throws GameProcessorException
     */
    public function turnAction(int $cellId) : Response
    {
        if (null === $cell = $this->getDoctrine()->getRepository('GameBundle:Cell')->find($cellId)) {
            throw new CellException(Response::HTTP_NOT_FOUND, "cell: {$cellId} doesn't exist");
        }
        if ($cell->hasFlag(CellModel::FLAG_DEAD)) {
            throw new CellException(Response::HTTP_UNPROCESSABLE_ENTITY, "cell: {$cellId} doesn't already flagged as *DEAD*");
        }

        $game = $this->get('battleship_game.service.game_processor')->processTurn($cell);
        $om   = $this->getDoctrine()->getManager();

        foreach (CellModel::getChangedCells() as $cell) {
            $om->persist($cell);
        }
        $om->flush();

        return $this->prepareSerializedResponse(new GameTurnResponse($game, CellModel::getChangedCells()));
    }
}
