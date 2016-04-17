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
            throw new \Exception('expected format:
            {
                data: %array of% {
                    player: {name: %string%},
                    cells: %array of% {
                        coordinate: %string%,
                        state: %int%
                    }
                }
            }');
        }
        $om = $this->getDoctrine()->getManager();
        $gameProcessor = $this->get('battleship.game.services.game.processor');
        $game = $gameProcessor->processGameInitiation($request->getContent());

        $om->persist($game);
        $om->flush();
        $response = $this->prepareSerializedResponse($game, Response::HTTP_CREATED);

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
        if ($cell->hasMask(CellModel::MASK_DEAD)) {
            throw new CellException("cell: {$cellId} doesn't have *LIVE* status");
        }

        $data = $this->get('battleship.game.services.game.processor')->processGameTurn($cell);
        $om = $this->getDoctrine()->getManager();

        foreach (CellModel::getChangedCells() as $cell) {
            $om->persist($cell);
        }
        $om->flush();

        return $this->prepareSerializedResponse($data);
    }

    private function validateInitRequest(Request $request) : bool
    {
        $content = $request->getContent();
        if (!is_string($content)) {
            return false;
        }
        $request = json_decode($content);
        if (!isset($request->data) || !is_array($request->data)) {
            return false;
        }

        foreach ($request->data as $data) {
            if (!isset($data->battlefield, $data->player->name, $data->cells) || !is_array($data->cells)) {
                return false;
            }
            foreach ($data->cells as $cell) {
                if (!isset($cell->coordinate, $cell->state)) {
                    return false;
                }
            }
        }

        return true;
    }
}
