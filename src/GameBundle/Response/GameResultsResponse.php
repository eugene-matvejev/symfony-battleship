<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\GameResult;
use JMS\Serializer\Annotation as Serializer;

/**
 * @since 5.0
 *
 * @Serializer\XmlRoot("game-results")
 * @Serializer\AccessorOrder(order="custom", custom={"results","meta"})
 */
class GameResultsResponse
{
    const META_INDEX_CURRENT_PAGE = 'currentPage';
    const META_INDEX_TOTAL_PAGES = 'totalPages';
    /**
     * @Serializer\Type("array<EM\GameBundle\Entity\GameResult>")
     * @Serializer\XmlList(entry="result")
     *
     * @var GameResult[]
     */
    private $results = [];
    /**
     * @Serializer\Type("array<string, integer>")
     * @Serializer\XmlKeyValuePairs()
     *
     * @var int[]
     */
    private $meta = [];

    /**
     * @return GameResult[]
     */
    public function getResults() : array
    {
        return $this->results;
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
}
