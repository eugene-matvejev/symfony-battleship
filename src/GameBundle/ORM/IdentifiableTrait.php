<?php

namespace EM\GameBundle\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * @since 1.0
 */
trait IdentifiableTrait
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
//
//    /**
//     * @param int $id
//     *
//     * @return $this
//     */
//    public function setId($id)
//    {
//        $this->id = $id;
//
//        return $this;
//    }
}