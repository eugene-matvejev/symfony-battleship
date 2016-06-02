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

    /**
     * @param string $json
     *
     * @throws \Exception
     */
    public function parse(string $json)
    {
        if (!$this->validate($json)) {
            throw new \Exception('invalid request');
        }

        $data = json_decode($json);

        $this->size = $data->size;
        $this->opponents = $data->opponents;
        $this->playerName = $data->playerName;
        $this->coordinates = $data->coordinates;
    }

    public function validate(string $json) : bool
    {
        $data = json_decode($json);

        return
            $data instanceof \stdClass
            && $this->validateStructure($data)
            && $this->validateBattlefieldSize($data->size)
            && $this->validatePlayerName($data->playerName)
            && $this->validateOpponentsAmount($data->opponents)
            && $this->validateCoordinates($data->coordinates);
    }

    public function validateStructure(\stdClass $data) : bool
    {
        return
            isset($data->opponents, $data->playerName, $data->size, $data->coordinates)
            && is_array($data->coordinates);
    }

    public function validateOpponentsAmount(int $value) : bool
    {
        return $value === 1;
    }

    public function validateBattlefieldSize(int $value) : bool
    {
        return 7 >= $value && $value <= 12;
    }

    public function validatePlayerName(string $value) : bool
    {
        return !empty($value);
    }

    public function validateCoordinates(array $coordinates) : bool
    {
        if (empty($coordinates)) {
            return false;
        }

        foreach ($coordinates as $coordinate) {
            if (empty($coordinate)) {
                return false;
            }
        }

        return true;
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
