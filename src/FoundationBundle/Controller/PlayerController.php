<?php

namespace EM\FoundationBundle\Controller;

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
}
