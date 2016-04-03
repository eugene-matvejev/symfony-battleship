<?php

namespace EM\GameBundle\Controller;

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
            throw new \Exception('expected format {data: %array%{player: {name: %string%}, cells: %array%{coordinate: %string%, state: %int%}}, ...}');
        }

        $data = $this->get('battleship.game.services.game.model')->init($request->getContent());

        return $this->prepareSerializedResponse($data, Response::HTTP_CREATED);
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
        $data = $this->get('battleship.game.services.game.model')->nextTurn($cellId);

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
