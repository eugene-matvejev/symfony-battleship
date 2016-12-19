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
 *          @ORM\Index(name="INDEX_PLAYER_EMAIL_AND_PASSWORD", columns={"email", "passwordHash"}),
 *      }
 * )
 *
 * @Serializer\AccessorOrder(order="custom", custom={"id", "flag", "email"})
 * @Serializer\XmlRoot("player")
 */
class Player extends AbstractFlaggedEntity
{
    /**
     * @ORM\Column(name="email", type="string")
     *
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected $email;
    /**
     * @since 23.0
     *
     * @ORM\Column(name="passwordHash", type="string", length=40)
     *
     * @Serializer\Exclude()
     *
     * @var string
     */
    protected $passwordHash;

    public function getEmail() : string
    {
        return $this->email;
    }

    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswordHash() : string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $hash) : self
    {
        $this->passwordHash = $hash;

        return $this;
    }
}
