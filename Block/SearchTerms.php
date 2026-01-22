<?php
/**
 * Amadeco PopularSearchTerms Module
 *
 * @category    Amadeco
 * @package     Amadeco_PopularSearchTerms
 * @author      Ilan Parmentier
 */

declare(strict_types=1);

namespace Amadeco\PopularSearchTerms\Block;

use Amadeco\PopularSearchTerms\Api\PopularTermsProviderInterface;
use Amadeco\PopularSearchTerms\Model\Config;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;

/**
 * Block class for Search Terms UI Component.
 *
 * Handles the merging of XML arguments and system configuration into 
 * the JsLayout object for KnockoutJS initialization.
 */
class SearchTerms extends Template
{
    /**
     * Constants for default values to ensure KISS/DRY.
     */
    private const DEFAULT_STORAGE_KEY = 'recent-searches';
    private const DEFAULT_FORM_ID = 'search_mini_form';

    /**
     * @param Context $context
     * @param PopularTermsProviderInterface $popularTermsProvider
     * @param Config $config
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly PopularTermsProviderInterface $popularTermsProvider,
        private readonly Config $config,
        private readonly SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Intercepts JsLayout to inject dynamic configuration before rendering.
     *
     * @return string
     */
    public function getJsLayout(): string
    {
        $layout = $this->serializer->unserialize(parent::getJsLayout());

        if (isset($layout['components']['search-terms'])) {
            $layout['components']['search-terms']['config'] = array_merge(
                $layout['components']['search-terms']['config'] ?? [],
                $this->getDynamicConfiguration()
            );
        }

        return (string)$this->serializer->serialize($layout);
    }

    /**
     * Check if the block functionality is enabled in system config.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * Resolves and compiles configuration from XML arguments and Providers.
     *
     * @return array<string, mixed>
     */
    private function getDynamicConfiguration(): array
    {
        return [
            'initialTerms'      => $this->getPopularTermsData(),
            'searchResultUrl'   => $this->_urlBuilder->getUrl('catalogsearch/result/'),
            'maxRecentSearches' => (int)($this->getData('max_recent_searches') ?: $this->config->getMaxRecentSearches()),
            'searchForm' => [
                'formId'     => $this->getData('search_form_id') ?: self::DEFAULT_FORM_ID,
                'inputName'  => $this->getData('search_input_name') ?: QueryFactory::QUERY_VAR_NAME,
                'storageKey' => $this->getData('storage_key') ?: self::DEFAULT_STORAGE_KEY,
            ]
        ];
    }

    /**
     * Fetches popular terms based on the 'number_of_terms' argument.
     *
     * @return array
     */
    private function getPopularTermsData(): array
    {
        $limit = (int)($this->getData('number_of_terms') ?: $this->config->getNumberOfTerms());
        return $this->popularTermsProvider->getPopularTerms(null, $limit);
    }
}
