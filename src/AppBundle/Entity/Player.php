<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use AppBundle\Library\Traits\Nameable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="players")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player
{
    use Identifiable, Nameable;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlayerType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=false)
     *
     * @var PlayerType
     */
    private $type;

    /**
     * @return PlayerType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param PlayerType $type
     *
     * @return $this
     */
    public function setType(PlayerType $type)
    {
        $this->type = $type;

        return $this;
    }
}