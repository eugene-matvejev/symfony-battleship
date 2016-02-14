<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Repository\GameResultRepository;

/**
 * @since 2.0
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

    function __construct(ObjectManager $om, int $perPage)
    {
        $this->gameResultRepository = $om->getRepository('GameBundle:GameResult');
        $this->gameResultsPerPage = $perPage;
    }

    public function overallStatistics(int $currentPage) : array
    {
        $data = [];
        foreach ($this->gameResultRepository->getAllOrderByDate($currentPage, $this->gameResultsPerPage) as $result) {
            $data[] = GameResultModel::getJSON($result);
        }

        return [
            'data' => $data,
            'meta' => [
                'config' => [
                    'perPage' => $this->gameResultsPerPage
                ],
                'page' => [
                    'curr' => $currentPage,
                    'total' => ceil($this->gameResultRepository->countTotalResults() / $this->gameResultsPerPage)
                ]
            ]
        ];
    }
}