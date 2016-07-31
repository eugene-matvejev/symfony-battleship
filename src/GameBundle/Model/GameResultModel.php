<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Repository\GameResultRepository;
use EM\GameBundle\Response\GameResultsResponse;

/**
 * @see   GameResultModelTest
 *
 * @since 5.0
 */
class GameResultModel
{
    /**
     * @var GameResultRepository
     */
    private $repository;
    /**
     * @var int
     */
    private $resultsPerPage;

    public function __construct(GameResultRepository $repository, int $recordsPerPage)
    {
        $this->repository = $repository;
        $this->resultsPerPage = $recordsPerPage;
    }

    public function buildResponse(int $currentPage) : GameResultsResponse
    {
        $totalPages = ceil($this->repository->countTotal() / $this->resultsPerPage);

        return (new GameResultsResponse())
            ->setResults($this->repository->getAllOrderByDate($currentPage, $this->resultsPerPage))
            ->setCurrentPage($currentPage)
            ->setTotalPages($totalPages ?: 1);
    }
}
