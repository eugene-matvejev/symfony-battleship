<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\GameResult;
use JMS\Serializer\Annotation as JMS;

/**
 * @since 5.0
 *
 * @JMS\AccessorOrder(order="custom", custom={"results","meta"})
 * @JMS\XmlRoot("game-results")
 */
class GameResultsResponse
{
    const META_INDEX_CURRENT_PAGE = 'currentPage';
    const META_INDEX_TOTAL_PAGES = 'totalPages';
    /**
     * @JMS\Type("array<string, integer>")
     * @JMS\XmlKeyValuePairs()
     *
     * @var int[]
     */
    private $meta = [];
    /**
     * @JMS\Type("array<EM\GameBundle\Entity\GameResult>")
     * @JMS\XmlList(entry="result")
     *
     * @var GameResult[]
     */
    private $results = [];

    /**
     * @return int[]
     */
    public function getMeta() : array
    {
        return $this->meta;
    }

    protected function setMeta(string $key, int $value) : self
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
        $this->setMeta(self::META_INDEX_CURRENT_PAGE, $value);

        return $this;
    }

    /**
     * @param GameResult[] $results
     *
     * @return GameResultsResponse
     */
    public function setResults(array $results) : self
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @return GameResult[]
     */
    public function getResults() : array
    {
        return $this->results;
    }
}
