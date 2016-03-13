<?php

namespace EM\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 2.0
 */
class StatisticsController extends Controller
{
    public function overallAction(int $page, string $format) : Response
    {
        $response = $this->get('battleship.game.services.statistics.model')->overallStatistics($page);

        return new Response($this->get('jms_serializer')->serialize($response, $format));
    }
}
