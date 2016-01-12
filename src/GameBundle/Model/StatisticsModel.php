<?php

namespace GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use GameBundle\Repository\GameResultRepository;

/**
 * @since 2.0
 */
class StatisticsModel
{
    const TIME_FORMAT       = 'd - m - Y / H:i';
    const RECORDS_PER_PAGE  = 10;
    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;

    /**
     * @param ObjectManager $om
     */
    function __construct(ObjectManager $om)
    {
        $this->gameResultRepository = $om->getRepository('GameBundle:GameResult');
    }

    /**
     * @param int $page
     *
     * @return array
     */
    public function overallStatistics(int $page) : array
    {
        $results = $this->gameResultRepository->getAllOrderByDate($page, self::RECORDS_PER_PAGE);
        $json = [];
        foreach ($results as $result) {
            $json[] = [
                'id'     => $result->getGame()->getId(),
                'time1'  => $result->getGame()->getTimestamp()->format(self::TIME_FORMAT),
                'time2'  => $result->getTimestamp()->format(self::TIME_FORMAT),
                'winner' => [
                    'name' => $result->getPlayer()->getName(),
                    'type' => $result->getPlayer()->getType()->getId()
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