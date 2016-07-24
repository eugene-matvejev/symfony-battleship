<?php

namespace EM\GameBundle\Controller;

use EM\GameBundle\Entity\PlayerSession;
use EM\GameBundle\Model\PlayerSessionModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 5.0
 */
abstract class AbstractAPIController extends Controller
{
    /**
     * build serialized response using JMS Serializer to serialize content
     *
     * @param mixed    $data
     * @param int      $status
     * @param string[] $headers
     *
     * @return Response
     */
    protected function prepareSerializedResponse($data, int $status = Response::HTTP_OK, array $headers = []) : Response
    {
        $session = $this->get('session')->get('_security_main');
        if ($session instanceof PlayerSession) {
            $headers[PlayerSessionModel::AUTHORIZATION_HEADER] = $session->getHash();
        }

        $header = $this->get('request_stack')->getMasterRequest()->headers->get('accept');
        $format = false !== strpos($header, 'application/xml') ? 'xml' : 'json';
        $headers['Content-Type'] = "application/{$format}";

        return new Response($this->get('jms_serializer')->serialize($data, $format), $status, $headers);
    }
}
