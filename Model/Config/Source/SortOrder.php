<?php
/**
 * Amadeco PopularSearchTerms Module
 *
 * @category   Amadeco
 * @package    Amadeco_PopularSearchTerms
 * @author     Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\PopularSearchTerms\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Sort Order Source Model
 */
class SortOrder implements OptionSourceInterface
{
    /**
     * Sort by popularity
     */
    public const SORT_BY_POPULARITY = 'popularity';

    /**
     * Sort by recency
     */
    public const SORT_BY_RECENCY = 'recency';

    /**
     * Get options array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::SORT_BY_POPULARITY, 'label' => __('Popularity (Most Popular First)')],
            ['value' => self::SORT_BY_RECENCY, 'label' => __('Recency (Most Recent First)')]
        ];
    }
}