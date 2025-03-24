<?php
/**
 * Amadeco PopularSearchTerms Module
 *
 * @category   Amadeco
 * @package    Amadeco_PopularSearchTerms
 * @author     Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\PopularSearchTerms\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Configuration helper for Popular Search Terms
 */
class Config extends AbstractHelper
{
    /**
     * XML path for enabled config
     */
    public const XML_PATH_ENABLED = 'catalog/popular_search_terms/enabled';

    /**
     * XML path for number of terms config
     */
    public const XML_PATH_NUMBER_OF_TERMS = 'catalog/popular_search_terms/number_of_terms';

    /**
     * XML path for sort order config
     */
    public const XML_PATH_SORT_ORDER = 'catalog/popular_search_terms/sort_order';

    /**
     * XML path for time period config
     */
    public const XML_PATH_TIME_PERIOD = 'catalog/popular_search_terms/time_period';

    /**
     * Default cache lifetime in seconds
     */
    public const DEFAULT_CACHE_LIFETIME = 86400; // 24 hours

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get number of terms to display
     *
     * @param int|null $storeId
     * @return int
     */
    public function getNumberOfTerms(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_NUMBER_OF_TERMS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get sort order
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSortOrder(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SORT_ORDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get time period in days
     *
     * @param int|null $storeId
     * @return int
     */
    public function getTimePeriod(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_TIME_PERIOD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}