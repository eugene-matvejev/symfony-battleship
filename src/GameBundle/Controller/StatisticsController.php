<?php

namespace EM\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @since 2.0
 */
class StatisticsController extends Controller
{
    public function overallAction(Request $request) : JsonResponse
    {
        return new JsonResponse($this->get('battleship.game.services.statistics.model')->overallStatistics($request->get('page', $request->get('page'))));
    }
}
