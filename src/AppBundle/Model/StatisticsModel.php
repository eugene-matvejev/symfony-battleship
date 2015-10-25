<?php

namespace AppBundle\Model;

use AppBundle\Repository\GameResultRepository;
use AppBundle\Entity\GameResult;

class StatisticsModel
{
    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;

    function __construct(GameResultRepository $repo)
    {
        $this->gameResultRepository = $repo;
    }

    /**
     * @return GameResult
     */
    public function overallStatistics()
    {
        return $this->gameResultRepository->findBy([], ['timestamp' => 'DESC']);
    }
}