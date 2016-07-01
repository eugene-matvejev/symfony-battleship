<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractFlaggedEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name="players",
 *      indexes={
 *          @ORM\Index(name="INDEX_PLAYER_EMAIL", columns={"email"}),
 *          @ORM\Index(name="INDEX_PLAYER_EMAIL_AND_PASSWORD", columns={"email", "password"}),
 *      }
 * )
 *
 * @Serializer\AccessorOrder(order="custom", custom={"id", "flag", "name"})
 * @Serializer\XmlRoot("player")
 */
class Player extends AbstractFlaggedEntity
{
    /**
     * @ORM\Column(name="email", type="string", length=25)
     *
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected $email;
    /**
     * @ORM\Column(name="password", type="string", length=40)
     *
     * @Serializer\Exclude()
     *
     * @var string
     */
    protected $password;

    public function getEmail() : string
    {
        return $this->email;
    }

    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function setPassword(string $password) : self
    {
        $this->password = $password;

        return $this;
    }
}
