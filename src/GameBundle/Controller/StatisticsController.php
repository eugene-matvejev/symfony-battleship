<?php

namespace EM\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @since 2.0
 */
class StatisticsController extends Controller
{
    public function overallAction(int $page) : JsonResponse
    {
        $json = $this->get('battleship.game.services.statistics.model')->overallStatistics($page);

        return new JsonResponse($json);
    }
}
