<?php

namespace GameBundle\Library\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * @since 1.0
 */
trait NameableTrait
{
    /**
     * @ORM\Column(name="name", type="string", nullable=false, length=200)
     *
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}