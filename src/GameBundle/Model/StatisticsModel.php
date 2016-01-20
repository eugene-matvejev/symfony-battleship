<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Repository\GameResultRepository;

/**
 * @since 2.0
 */
class StatisticsModel
{
    const RECORDS_PER_PAGE  = 10;
    /**
     * @var GameResultRepository
     */
    private $gameResultRepository;

    function __construct(ObjectManager $om)
    {
        $this->gameResultRepository = $om->getRepository('GameBundle:GameResult');
    }

    public function overallStatistics(int $page) : array
    {
        $json = [];
        foreach ($this->gameResultRepository->getAllOrderByDate($page, self::RECORDS_PER_PAGE) as $result) {
            $json[] = GameResultModel::getJSON($result);
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