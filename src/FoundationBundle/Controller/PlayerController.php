<?php

namespace EM\FoundationBundle\Controller;

use EM\FoundationBundle\Authorization\Token\PlayerSessionToken;
use EM\GameBundle\Entity\Player;
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
    /**
     * @see PlayerControllerTest::indexAction
     *
     * @return Response
     */
    public function indexAction() : Response
    {
        return $this->prepareSerializedResponse([]);
    }

    /**
     * @since 23.0
     *
     * @ApiDoc(
     *      section = "API: Foundation",
     *      description = "Creates a new player from submitted data",
     *      input = "",
     *      responseMap = {
     *          201 = "EM\GameBundle\Entity\PlayerSession",
     *          422 = ""
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
        if (!isset($json->email, $json->password)) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $player = $this->getDoctrine()->getRepository(Player::class)->findOneBy(['email' => $json->email]);
        if (null !== $player) {
            throw new PlayerException(Response::HTTP_UNPROCESSABLE_ENTITY, "player with {$json->email} already exists");
        }

        $player = $this
            ->get('battleship_game.service.player_model')
            ->createPlayer($json->email, $json->password);

        $om = $this->getDoctrine()->getManager();
        $om->persist($player);
        $om->flush();

        return $this->processLogin($json->email, $json->password);
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
        if (!isset($json->email, $json->password)) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

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
    private function processLogin(string $email, string $password) : Response
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
     * @return Response
     * @throws PlayerException
     */
    public function logoutAction() : Response
    {
        /** @var PlayerSessionToken $token */
        $token   = $this->get('security.token_storage')->getToken();
        $session = $token->getSession();

        $om = $this->getDoctrine()->getManager();
        $om->remove($session);
        $om->flush();

        return $this->prepareSerializedResponse([], Response::HTTP_ACCEPTED);
    }
}
