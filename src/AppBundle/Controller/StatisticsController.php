<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StatisticsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function overallAction(Request $request)
    {
        return new JsonResponse($this->get('battleship.game.services.statistics.model')->overallStatistics($request->get('page', $request->get('page'))));
    }
}
