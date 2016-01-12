<?php

namespace GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use GameBundle\Entity\Player;
use GameBundle\Entity\PlayerType;
use GameBundle\Repository\PlayerTypeRepository;

/**
 * @since 2.0
 */
class PlayerModel
{
    const TYPE_CPU   = 1;
    const TYPE_HUMAN = 2;
    /**
     * @var PlayerTypeRepository
     */
    private $playerTypeRepository;
    /**
     * @var PlayerType[]
     */
    private static $playerTypes;

    function __construct(ObjectManager $om)
    {
        $this->playerTypeRepository = $om->getRepository('GameBundle:PlayerType');
    }

    /**
     * @return PlayerType[]
     */
    public function getTypes() : array
    {
        if(null === self::$playerTypes) {
            self::$playerTypes = $this->playerTypeRepository->getTypes();
        }

        return self::$playerTypes;
    }

    /**
     * @return int[]
     */
    public static function getAllTypes() : array
    {
        return [self::TYPE_CPU, self::TYPE_HUMAN];
    }

    public static function getJSON(Player $player) : \stdClass
    {
        $std = new \stdClass();
        $std->id = $player->getId();
        $std->type = $player->getType()->getId();
        $std->name = $player->getName();

        return $std;
    }
}