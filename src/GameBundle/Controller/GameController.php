<?php

namespace EM\GameBundle\Controller;

use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Exception\PlayerException;
use EM\GameBundle\Model\CellModel;
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
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function initAction(Request $request) : Response
    {
        if (!$this->validateInitRequest($request)) {
            throw new \Exception('unexpected request content');
        }

        $om = $this->getDoctrine()->getManager();
        $gameProcessor = $this->get('battleship.game.services.game.processor');
        $game = $gameProcessor->buildGame($request->getContent());

        $om->persist($game);
        $om->flush();
        $response = $this->buildSerializedResponse($game, Response::HTTP_CREATED);

        foreach ($gameProcessor->processCPUBattlefieldsInitiation($game) as $cell) {
            $om->persist($cell);
        }
        $om->flush();

        return $response;
    }

    /**
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

    private function validateInitRequest(Request $request) : bool
    {
        $request = json_decode($request->getContent());

        if (!is_array($request)) {
            return false;
        }

        foreach ($request as $player) {
            if (!isset($player->name, $player->flags, $player->cells) || !is_array($player->cells)) {
                return false;
            }

            foreach ($player->cells as $cell) {
                if (!isset($cell->coordinate, $cell->flags)) {
                    return false;
                }
            }
        }

        return true;
    }
}
