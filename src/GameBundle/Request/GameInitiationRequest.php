<?php

namespace EM\GameBundle\Request;

use JMS\Serializer\Annotation as JMS;

/**
 * @since 18.0
 */
class GameInitiationRequest
{
    /**
     * @JMS\Type("integer")
     *
     * @var int
     */
    private $opponents;
    /**
     * @JMS\Type("integer")
     *
     * @var int
     */
    private $size;
    /**
     * @JMS\Type("array<string>")
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

    /**
     * @return string[]
     */
    public function getCoordinates() : array
    {
        return $this->coordinates;
    }
}
