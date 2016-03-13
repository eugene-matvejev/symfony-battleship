<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Repository\GameResultRepository;
use EM\GameBundle\Response\StatisticsResponse;

/**
 * @since 5.0
 */
class StatisticsModel
{
    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;
    /**
     * @var int
     */
    private $gameResultsPerPage;

    function __construct(int $recordsPerPage, GameResultRepository $repository)
    {
        $this->gameResultsPerPage = $recordsPerPage;
        $this->gameResultRepository = $repository;
    }

    public function overallStatistics(int $currentPage) : StatisticsResponse
    {
        return (new StatisticsResponse())
            ->setData($this->gameResultRepository->getAllOrderByDate($currentPage, $this->gameResultsPerPage))
            ->setCurrentPage($currentPage)
            ->setTotalPages(ceil($this->gameResultRepository->countTotalResults() / $this->gameResultsPerPage));
    }
}
