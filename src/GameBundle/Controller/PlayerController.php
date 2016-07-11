<?php

namespace EM\GameBundle\Controller;

use EM\GameBundle\Entity\PlayerSession;
use EM\GameBundle\Exception\PlayerException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see   GameController
 *
 * @since 22.0
 */
class PlayerController extends AbstractAPIController
{
    /**
     * @ApiDoc(
     *      section = "API: Foundation",
     *      description = "Creates a new player from submitted data",
     *      input = "",
     *      responseMap = {
     *          201 = "EM\GameBundle\Entity\Player"
     *      }
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws PlayerException
     */
    public function registerAction(Request $request) : Response
    {
        $json = json_decode($request->getContent());

        $player = $this
            ->get('battleship_game.service.player_model')
            ->createOnRequestHumanControlled($json->email, $json->password);

        if (null !== $player->getId()) {
            throw new PlayerException("player with {$json->email} already exists");
        }

        $session = $this->get('battleship_game.service.player_session_model')->create($player);

        $om = $this->getDoctrine()->getManager();
        $om->persist($player);
        $om->persist($session);
        $this->setPlayerSession($session);

        $om->flush();

        return $this->prepareSerializedResponse($player, Response::HTTP_CREATED);
    }
    /**
     * @ApiDoc(
     *      section = "API: Foundation",
     *      description = "authenticate and returns player details",
     *      input = "",
     *      responseMap = {
     *          200 = "EM\GameBundle\Entity\Player"
     *      }
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws PlayerException
     */

    public function loginAction(Request $request)
    {
        $json = json_decode($request->getContent());

        $session = $this->get('battleship_game.service.player_session_model')->authenticate($json->email, $json->password);

        $om = $this->getDoctrine()->getManager();
        $om->persist($session);
        $this->setPlayerSession($session);

        $om->flush();

        return $this->prepareSerializedResponse($session->getPlayer());
    }

    protected function setPlayerSession(PlayerSession $session)
    {
        $this->container->get('session')->set('_security_main', $session);
    }
}
