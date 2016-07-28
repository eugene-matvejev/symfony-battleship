<?php

namespace EM\FoundationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 22.3
 */
abstract class AbstractAPIController extends Controller
{
    /**
     * build serialized response using @see Serializer [JMS Serializer] to serialize content
     *
     * @param mixed    $data
     * @param int      $status
     * @param string[] $headers
     *
     * @return Response 
     */
    protected function prepareSerializedResponse($data, int $status = Response::HTTP_OK, array $headers = []) : Response
    {
        $header = $this->get('request_stack')->getMasterRequest()->headers->get('accept');
        $format = false !== strpos($header, 'application/xml') ? 'xml' : 'json';
        $headers['Content-Type'] = "application/{$format}";

        return new Response($this->get('jms_serializer')->serialize($data, $format), $status, $headers);
    }
}
