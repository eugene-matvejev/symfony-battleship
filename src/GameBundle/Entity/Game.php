<?php

namespace EM\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\IdentifiableInterface;
use EM\GameBundle\ORM\IdentifiableTrait;
use EM\GameBundle\ORM\TimestampedInterface;
use EM\GameBundle\ORM\TimestampedTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="games")
 * @ORM\HasLifecycleCallbacks
 */
class Game implements IdentifiableInterface, TimestampedInterface
{
    use IdentifiableTrait, TimestampedTrait;
    /**
     * @ORM\OneToMany(targetEntity="EM\GameBundle\Entity\Battlefield", mappedBy="game", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="game", nullable=false)
     *
     * @var Battlefield[]
     */
    private $battlefields;
    /**
     * @ORM\OneToOne(targetEntity="EM\GameBundle\Entity\GameResult", mappedBy="game", cascade={"persist"})
     *
     * @var GameResult
     */
    private $result;

    public function __construct()
    {
        $this->battlefields = new ArrayCollection();
    }

    public function addBattlefield(Battlefield $battlefield) : self
    {
        $battlefield->setGame($this);
        $this->battlefields->add($battlefield);

        return $this;
    }

    public function removeBattlefield(Battlefield $battlefield) : self
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

    public function setResult(GameResult $result) : self
    {
        $result->setGame($this);
        $this->result = $result;

        return $this;
    }
}
