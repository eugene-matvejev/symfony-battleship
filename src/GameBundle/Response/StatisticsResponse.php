<?php

namespace EM\GameBundle\Response;

/**
 * @since 5.0
 */
class StatisticsResponse extends AbstractResponse
{
    const META_INDEX_TOTAL_PAGES = 'totalPages';
    const META_INDEX_PER_PAGE = 'perPages';
    const META_INDEX_CURRENT_PAG = 'currentPages';
    /**
     * @var string[]|int[]
     */
    private $meta = [];

    public function getMeta() : array
    {
        return $this->meta;
    }

    protected function setMeta(string $key, string $value) : self
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function setTotalPages(int $value) : self
    {
        $this->setMeta(self::META_INDEX_TOTAL_PAGES, $value);

        return $this;
    }

    public function setCurrentPage(int $value) : self
    {
        $this->setMeta(self::META_INDEX_PER_PAGE, $value);

        return $this;
    }
}
