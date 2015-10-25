<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatisticsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function statsAction()
    {
        return new JsonResponse($this->get('battleship.game.services.statistics.model')->overallStatistics());
    }
}
