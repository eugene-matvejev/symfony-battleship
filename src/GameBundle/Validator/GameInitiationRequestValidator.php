<?php

namespace EM\GameBundle\Validator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @since 18.0
 */
class GameInitiationRequestValidator
{
    /**
     * @var int
     */
    private $minBattlefieldSize;
    /**
     * @var int
     */
    private $maxBattlefieldSize;
    /**
     * @var int
     */
    private $minOpponents;
    /**
     * @var int
     */
    private $maxOpponents;

    /**
     * @param int $minBattlefieldSize
     * @param int $maxBattlefieldSize
     * @param int $minOpponents
     * @param int $maxOpponents
     */
    public function __construct(int $minBattlefieldSize, int $maxBattlefieldSize, int $minOpponents, int $maxOpponents)
    {
        $this->minBattlefieldSize = $minBattlefieldSize;
        $this->maxBattlefieldSize = $maxBattlefieldSize;
        $this->minOpponents = $minOpponents;
        $this->maxOpponents = $maxOpponents;
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

    protected function validateStructure(\stdClass $data) : bool
    {
        return
            isset($data->opponents, $data->playerName, $data->size, $data->coordinates)
            && is_array($data->coordinates);
    }

    protected function validateOpponentsAmount(int $value) : bool
    {
        return $this->isBetween($value, $this->minOpponents, $this->maxOpponents);
    }

    protected function validateBattlefieldSize(int $value) : bool
    {
        return $this->isBetween($value, $this->minBattlefieldSize, $this->maxBattlefieldSize);
    }

    // TODO: replace name with ID
    protected function validatePlayerName(string $value) : bool
    {
        return !empty($value);
    }

    protected function validateCoordinates(array $coordinates) : bool
    {
        if (empty($coordinates)) {
            return false;
        }

        foreach ($coordinates as $coordinate) {
            if (!$this->validateCoordinate($coordinate)) {
                return false;
            }
        }

        return true;
    }

    protected function validateCoordinate(string $coordinate) : bool
    {
        return 0 !== preg_match('/[A-Z][1-9][0-9]*/', $coordinate);
    }

    protected function isBetween(int $value, int $min, int $max) : bool
    {
        return $min <= $value && $value <= $max;
    }
}
