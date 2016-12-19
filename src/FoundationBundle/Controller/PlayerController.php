<?php

namespace EM\FoundationBundle\Controller;

use EM\FoundationBundle\Entity\User;
use EM\FoundationBundle\Security\Authorization\Token\WsseToken;
use EM\GameBundle\Exception\UserException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @see   PlayerControllerTest
 *
 * @since 22.3
 */
class UserController extends AbstractAPIController
{
    /**
     * @since 23.0
     *
     * @ApiDoc(
     *      section = "API: Foundation",
     *      description = "Creates a new player from submitted data",
     *      input = "",
     *      responseMap = {
     *          201 = "EM\FoundationBundle\Entity\UserSession"
     *      },
     *      statusCodes = {
     *          201 = "successful registration",
     *          400 = "bad request",
     *          422 = "player with same email already exists"
     *      }
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws UserException
     */
    public function registerAction(Request $request) : Response
    {
        $json = json_decode($request->getContent());
        if (!isset($json->email, $json->password)) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $player = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $json->email]);
        if (null !== $player) {
            throw new UserException(Response::HTTP_UNPROCESSABLE_ENTITY, "player with {$json->email} already exists");
        }

        $player = $this
            ->get('em.foundation_bundle.model.player')
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
     *          201 = "EM\FoundationBundle\Entity\UserSession",
     *      },
     *      statusCodes = {
     *          201 = "successfull login",
     *          400 = "bad request",
     *          401 = "unsucessfull authorization"
     *      }
     * )
     *
     * @param Request $request
     *
     * @return Response
     * @throws UserException
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
        try {
            $session = $this->get('em.foundation_bundle.model.player_session')->authenticate($email, $password);

            $om = $this->getDoctrine()->getManager();
            $om->persist($session);
            $om->flush();

            return $this->prepareSerializedResponse($session, Response::HTTP_CREATED);
        } catch (BadCredentialsException $e) {
            return new Response(null, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @since 23.0
     *
     * @Security("has_role('PLAYER')")
     * @ApiDoc(
     *      section = "API: Foundation",
     *      description = "deletes session from database",
     *      input = "",
     *      statusCodes = {
     *          202 = "successful logout",
     *          403 = "not authorized"
     *      }
     * )
     *
     * @return Response
     * @throws UserException
     */
    public function logoutAction() : Response
    {
        /** @var WsseToken $token */
        $token   = $this->get('security.token_storage')->getToken();
        $session = $token->getSession();

        $om = $this->getDoctrine()->getManager();
        $om->remove($session);
        $om->flush();

        return $this->prepareSerializedResponse([], Response::HTTP_ACCEPTED);
    }
}
