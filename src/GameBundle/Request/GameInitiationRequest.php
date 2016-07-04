<?php

namespace EM\GameBundle\Request;

use JMS\Serializer\Annotation as Serializer;

/**
 * @since 18.0
 */
class GameInitiationRequest
{
    /**
     * @Serializer\Type("integer")
     *
     * @var int
     */
    private $opponents;
    /**
     * @Serializer\Type("integer")
     *
     * @var int
     */
    private $size;
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("playerName")
     *
     * @var string
     */
    private $playerName;
    /**
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private $coordinates;

    public function __construct(string $json)
    {
        $this->parse($json);
    }

    public function parse(string $json) : self
    {
        $data = json_decode($json);

        $this->size = $data->size;
        $this->opponents = $data->opponents;
        $this->playerName = $data->playerName;
        $this->coordinates = $data->coordinates;

        return $this;
    }

    public function getOpponents() : int
    {
        return $this->opponents;
    }

    public function getSize() : int
    {
        return $this->size;
    }

    public function getPlayerName() : string
    {
        return $this->playerName;
    }

    /**
     * @return string[]
     */
    public function getCoordinates() : array
    {
        return $this->coordinates;
    }
}
