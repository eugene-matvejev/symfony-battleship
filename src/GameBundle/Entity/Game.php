<?php

namespace EM\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;
use EM\GameBundle\ORM\TimestampedInterface;
use EM\GameBundle\ORM\TimestampedTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(name="games")
 * @ORM\HasLifecycleCallbacks()
 */
class Game extends AbstractEntity implements TimestampedInterface
{
    use TimestampedTrait;
    /**
     * @ORM\OneToMany(targetEntity="EM\GameBundle\Entity\Battlefield", mappedBy="game", cascade={"persist"}, fetch="EAGER", indexBy="id")
     * @ORM\JoinColumn(name="id", referencedColumnName="game", nullable=false)
     *
     * @var Collection|Battlefield[]
     */
    protected $battlefields;
    /**
     * @ORM\OneToOne(targetEntity="EM\GameBundle\Entity\GameResult", mappedBy="game", cascade={"persist"}, fetch="EAGER")
     *
     * @var GameResult
     */
    protected $result;

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

    /**
     * @return Collection|Battlefield[]
     */
    public function getBattlefields() : Collection
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
