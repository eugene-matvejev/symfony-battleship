<?php

namespace EM\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 5.0
 */
abstract class AbstractAPIController extends Controller
{
    protected function prepareSerializedOutput(Request $request, $data, int $httpStatusCode = Response::HTTP_OK, array $headers = []) : Response
    {
        $serialized = (false !== strpos($request->headers->get('accept'), 'application/xml'))
            ? $this->get('jms_serializer')->serialize($data, 'xml')
            : $this->get('jms_serializer')->serialize($data, 'json');

        return new Response($serialized, $httpStatusCode, $headers);
    }
}
