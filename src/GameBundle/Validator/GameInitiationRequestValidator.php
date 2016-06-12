<?php

namespace EM\GameBundle\Validator;

/**
 * @since 18.0
 *
 * @see   GameInitiationRequestValidatorTest
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
            $this->validateStructure($data)
            && $this->validatePlayerName($data->playerName)
            && $this->validateBattlefieldSize($data->size)
            && $this->validateOpponentsAmount($data->opponents)
            && $this->validateCoordinates($data->coordinates);
    }

    protected function validateStructure($data) : bool
    {
        return
            $data instanceof \stdClass &&
            isset($data->opponents, $data->playerName, $data->size, $data->coordinates)
            && is_array($data->coordinates);
    }

    protected function validatePlayerName(string $value) : bool
    {
        return !empty($value);
    }

    protected function validateBattlefieldSize(int $value) : bool
    {
        return $this->isBetween($value, $this->minBattlefieldSize, $this->maxBattlefieldSize);
    }

    protected function validateOpponentsAmount(int $value) : bool
    {
        return $this->isBetween($value, $this->minOpponents, $this->maxOpponents);
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
