<?php

namespace EM\FoundationBundle\Controller;

use EM\GameBundle\Exception\PlayerException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see   PlayerControllerTest
 *
 * @since 22.3
 */
class PlayerController extends AbstractAPIController
{
    public function indexAction() : Response
    {
        return $this->redirectToRoute('nelmio_api_doc_index', ['view' => 'default']);
    }

    /**
     * @since 23.0
     *
     * @ApiDoc(
     *      section = "API: Foundation",
     *      description = "Creates a new player from submitted data",
     *      input = "",
     *      responseMap = {
     *          201 = "EM\GameBundle\Entity\PlayerSession"
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

        $om = $this->getDoctrine()->getManager();
        $om->persist($player);
        $om->flush();

        return $this->processLogin($json->email, $json->password);
    }

    /**
     * @since 23.0
     *
     * @param string $email
     * @param string $password
     *
     * @return Response
     */
    protected function processLogin(string $email, string $password) : Response
    {
        $session = $this->get('battleship_game.service.player_session_model')->authenticate($email, $password);

        $om = $this->getDoctrine()->getManager();
        $om->persist($session);

        $om->flush();

        return $this->prepareSerializedResponse($session, Response::HTTP_CREATED);
    }

    /**
     * @since 23.0
     *
     * @ApiDoc(
     *      section = "API: Foundation",
     *      description = "authenticate and returns player details",
     *      input = "",
     *      responseMap = {
     *          201 = "EM\GameBundle\Entity\PlayerSession"
     *      }
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws PlayerException
     */
    public function loginAction(Request $request) : Response
    {
        $json = json_decode($request->getContent());

        return $this->processLogin($json->email, $json->password);
    }

    /**
     * @since 23.0
     *
     * @Security("has_role('PLAYER')")
     * @ApiDoc(
     *      section = "API: Foundation",
     *      description = "deletes session from database",
     *      input = "",
     *      responseMap = {
     *          202 = ""
     *      }
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws PlayerException
     */
    public function logoutAction(Request $request) : Response
    {
        $json = json_decode($request->getContent());

        $session = $this->get('battleship_game.service.player_session_model')->authenticate($json->email, $json->password);

        $om = $this->getDoctrine()->getManager();
        $om->detach($session);
        $om->flush();

        return $this->prepareSerializedResponse([], Response::HTTP_ACCEPTED);
    }
}
