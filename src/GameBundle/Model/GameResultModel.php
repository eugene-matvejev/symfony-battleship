<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Repository\GameResultRepository;
use EM\GameBundle\Response\GameResultsResponse;
use EM\GameBundle\Response\StatisticsResponse;

/**
 * @since 5.0
 */
class GameResultModel
{
    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;
    /**
     * @var int
     */
    private $gameResultsPerPage;

    public function __construct(int $recordsPerPage, GameResultRepository $repository)
    {
        $this->gameResultsPerPage = $recordsPerPage;
        $this->gameResultRepository = $repository;
    }

    public function prepareResponse(int $currentPage) : GameResultsResponse
    {
        return (new GameResultsResponse())
            ->setResults($this->gameResultRepository->getAllOrderByDate($currentPage, $this->gameResultsPerPage))
            ->setCurrentPage($currentPage)
            ->setTotalPages(ceil($this->gameResultRepository->countTotalResults() / $this->gameResultsPerPage));
    }
}
