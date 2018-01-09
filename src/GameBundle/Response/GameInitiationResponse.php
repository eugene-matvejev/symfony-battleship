<?php

namespace EM\GameBundle\Response;

use Doctrine\Common\Collections\Collection;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Model\CellModel;
use EM\FoundationBundle\Model\UserModel;
use JMS\Serializer\Annotation as JMS;

/**
 * @see   GameInitiationResponseTest
 *
 * @since 5.0
 *
 * @JMS\XmlRoot("battlefields")
 * @JMS\AccessorOrder(order="custom", custom={"results","meta"})
 */
class GameInitiationResponse
{
    /**
     * @JMS\Type("array<EM\GameBundle\Entity\Battlefield>")
     * @JMS\XmlList(entry="battlefield")
     * @JMS\Inline()
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

        if (UserModel::isAIControlled($battlefield->getUser())) {
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
