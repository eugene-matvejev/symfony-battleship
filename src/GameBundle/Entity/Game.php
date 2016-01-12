<?php

namespace GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\ORM\IdentifiableInterface;
use GameBundle\Library\ORM\TimestampedInterface;
use GameBundle\Library\ORM\IdentifiableTrait;
use GameBundle\Library\ORM\TimestampedTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(name="games")
 * @ORM\HasLifecycleCallbacks
 */
class Game implements IdentifiableInterface, TimestampedInterface
{
    use IdentifiableTrait, TimestampedTrait;
    /**
     * @ORM\OneToMany(targetEntity="GameBundle\Entity\Battlefield", mappedBy="game", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="game", nullable=false)
     *
     * @var Battlefield[]
     */
    private $battlefields;
    /**
     * @ORM\OneToOne(targetEntity="GameBundle\Entity\GameResult", mappedBy="game", cascade={"persist"})
     *
     * @var GameResult
     */
    private $result;

    public function __construct()
    {
        $this->battlefields = new ArrayCollection();
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return $this
     */
    public function addBattlefield(Battlefield $battlefield)
    {
        $battlefield->setGame($this);
        $this->battlefields->add($battlefield);

        return $this;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return $this
     */
    public function removeBattlefield(Battlefield $battlefield)
    {
        $this->battlefields->removeElement($battlefield);

        return $this;
    }

    /**
     * @return Battlefield[]
     */
    public function getBattlefields()
    {
        return $this->battlefields;
    }

    /**
     * @return GameResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param GameResult $result
     *
     * @return $this
     */
    public function setResult(GameResult $result)
    {
        $result->setGame($this);
        $this->result = $result;

        return $this;
    }
}