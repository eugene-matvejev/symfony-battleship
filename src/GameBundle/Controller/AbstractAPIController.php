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
    protected function prepareSerializedOutput($data, int $status = Response::HTTP_OK, array $headers = []) : Response
    {
        $format = (false !== strpos($this->get('request')->headers->get('accept'), 'application/xml')) ? 'xml' : 'json';
        $headers['Content-Type'] = 'application/' . $format;

        return new Response($this->get('jms_serializer')->serialize($data, $format), $status, $headers);
    }
}
