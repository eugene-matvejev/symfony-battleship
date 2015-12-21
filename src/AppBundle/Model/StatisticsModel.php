<?php

namespace AppBundle\Model;

use AppBundle\Repository\GameResultRepository;
use AppBundle\Entity\GameResult;
use Doctrine\Common\Persistence\ObjectManager;

class StatisticsModel
{
    const TIME_FORMAT       = 'd - m - Y / H:i';
    const RECORDS_PER_PAGE  = 10;
    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;

    function __construct(ObjectManager $om)
    {
        $this->gameResultRepository = $om->getRepository('AppBundle:GameResult');
    }

    /**
     * @param int|null $page
     *
     * @return mixed[]
     */
    public function overallStatistics(\int $page) : array
    {
        $results = $this->gameResultRepository->getResultsInDescendingDate($page, self::RECORDS_PER_PAGE);
        $json = [];
        foreach ($results as $result) {
            $json[] = [
                'id'     => $result->getGame()->getId(),
                'time1'  => $result->getGame()->getTimestamp()->format(self::TIME_FORMAT),
                'time2'  => $result->getTimestamp()->format(self::TIME_FORMAT),
                'winner' => [
                    'name' => $result->getWinner()->getName(),
                    'type' => $result->getWinner()->getType()->getId()
                ]
            ];
        }

        return [
            'data' => $json,
            'meta' => [
                'config' => [
                    'perPage' => self::RECORDS_PER_PAGE
                ],
                'page' => [
                    'curr' => $page,
                    'total' => ceil($this->gameResultRepository->countTotalResults() / self::RECORDS_PER_PAGE)
                ]
            ]
        ];
    }
}