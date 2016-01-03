<?php

namespace GameBundle\Library\Traits;

trait Nameable
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