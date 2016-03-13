<?php

namespace EM\GameBundle\Response;

/**
 * @since 5.0
 */
abstract class AbstractResponse
{
    /**
     * @var array
     */
    protected $data = [];

    public function getData() : array
    {
        return $this->data;
    }

    public function setData(array $data) : self
    {
        $this->data = $data;

        return $this;
    }
}
