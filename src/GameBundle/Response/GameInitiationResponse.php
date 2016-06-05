<?php

namespace EM\GameBundle\Response;

use Doctrine\Common\Collections\Collection;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\PlayerModel;
use JMS\Serializer\Annotation as Serializer;

/**
 * @since 5.0
 *
 * @Serializer\XmlRoot("battlefields")
 * @Serializer\AccessorOrder(order="custom", custom={"results","meta"})
 */
class GameInitiationResponse
{
    /**
     * @Serializer\Type("array<EM\GameBundle\Entity\Battlefield>")
     * @Serializer\XmlList(entry="battlefield")
     * @Serializer\Inline()
     *
     * @var Battlefield[]
     */
    private $battlefields = [];

    /**
     * @param Collection|Battlefield[] $battlefields
     */
    public function __construct(Collection $battlefields)
    {
        foreach ($battlefields as $battlefield) {
            $this->addBattlefield($battlefield);
        }
    }

    public function addBattlefield(Battlefield $battlefield) : self
    {
        $this->battlefields[] = $battlefield;

        if (PlayerModel::isAIControlled($battlefield->getPlayer())) {
            foreach ($battlefield->getCells() as $cell) {
                $cell->setFlags(CellModel::FLAG_NONE);
            }
        }

        return $this;
    }

    /**
     * @return Battlefield[]
     */
    public function getBattlefields() : array
    {
        return $this->battlefields;
    }
}
