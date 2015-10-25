<?php

namespace AppBundle\Model;

use AppBundle\Repository\GameResultRepository;
use AppBundle\Entity\GameResult;

class StatisticsModel
{
    const TIME_FORMAT = 'd - m - Y / H:i';
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
        $results = $this->gameResultRepository->findBy([], ['timestamp' => 'DESC']);
        $json = [];
        foreach ($results as $gameResult) {
            /**
             * @var $gameResult GameResult
             */
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