<?php

namespace EM\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 1.0
 */
class GameController extends Controller
{
    public function indexAction() : Response
    {
        return $this->render('@Game/index.html.twig');
    }

    public function initAction(Request $request, string $format) : Response
    {
        if (empty($request->getContent())) {
            return new JsonResponse();
        }

        $game = $this->get('battleship.game.services.game.model')->init($request->getContent());

        return new Response($this->get('jms_serializer')->serialize($game, $format), Response::HTTP_CREATED);
    }

    public function turnAction(Request $request, string $format) : Response
    {
        if (empty($request->getContent())) {
            return new JsonResponse();
        }

        $response = $this->get('battleship.game.services.game.model')->nextTurn($request->getContent());

        return new Response($this->get('jms_serializer')->serialize($response, $format));
    }
}
