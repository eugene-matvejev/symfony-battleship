<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use AppBundle\Library\Traits\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="games")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
{
    use Identifiable, Timestampable;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Battlefield", mappedBy="game", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="game", nullable=false)
     *
     * @var ArrayCollection|Battlefield[]
     */
    private $battlefields;
    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\GameResult", mappedBy="game", cascade={"persist"})
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