<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\GameResult;
use JMS\Serializer\Annotation as JMS;

/**
 * EM\GameBundle\Response\GameResultsResponse:
 * accessor_order: custom
 * custom_accessor_order: [meta, results]
 *
 * xml_root_name: statistics
 *
 * properties:
 * meta:
 * type: array<string, integer>
 * inline: false
 * xml_key_value_pairs: true
 * results:
 * type: array<EM\GameBundle\Entity\GameResult>
 * xml_list:
 * inline: false
 * entry_name: result
 */

/**
 * @since 5.0
 *
 * @JMS\AccessorOrder("custom", custom={"results","meta"})
 * @JMS\XmlRoot("statistics")
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
