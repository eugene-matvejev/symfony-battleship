<?php

namespace AppBundle\Model;

use AppBundle\Repository\GameResultRepository;
use AppBundle\Entity\GameResult;

class StatisticsModel
{
    const TIME_FORMAT       = 'd - m - Y / H:i';
    const RECORDS_PER_PAGE  = 10;
    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;

    function __construct(GameResultRepository $repo)
    {
        $this->gameResultRepository = $repo;
    }

    /**
     * @param int|null $page
     *
     * @return mixed[]
     */
    public function overallStatistics(\int $page = 1) : array
    {
        $results = $this->gameResultRepository->getResultsInDescendingDate($page, self::RECORDS_PER_PAGE);
        $json = [];
        foreach ($results as $gameResult) {
            $json[] = [
                'id'     => $gameResult->getGame()->getId(),
                'time1'  => $gameResult->getGame()->getTimestamp()->format(self::TIME_FORMAT),
                'time2'  => $gameResult->getTimestamp()->format(self::TIME_FORMAT),
                'winner' => [
                    'name' => $gameResult->getWinner()->getName(),
                    'type' => $gameResult->getWinner()->getType()->getId()
                ]
            ];
        }

        return $json;
    }
}