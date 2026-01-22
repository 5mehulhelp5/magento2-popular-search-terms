<?php
/**
 * Amadeco PopularSearchTerms Module
 *
 * @category    Amadeco
 * @package     Amadeco_PopularSearchTerms
 * @author      Ilan Parmentier
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Amadeco\PopularSearchTerms\Block;

use Amadeco\PopularSearchTerms\Api\PopularTermsProviderInterface;
use Amadeco\PopularSearchTerms\Model\Config;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Block for Popular Search Terms UI Component.
 *
 * This block enriches the static JsLayout configuration defined in XML with 
 * dynamic search terms and store-specific URLs.
 */
class SearchTerms extends Template
{
    /**
     * Default number of terms if none provided in XML or System Config.
     */
    private const DEFAULT_TERMS_LIMIT = 5;

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
     * Enriches the JsLayout 'config' with dynamic search data.
     *
     * Injects:
     * - initialTerms: The list of popular terms from the provider.
     * - searchResultUrl: The base URL for catalog search results.
     *
     * @return string JSON serialized layout configuration.
     */
    public function getJsLayout(): string
    {
        $layout = $this->serializer->unserialize(parent::getJsLayout());
        
        if (isset($layout['components']['search-terms'])) {
            $component = &$layout['components']['search-terms'];
            $config = $component['config'] ?? [];

            // Merge dynamic data into existing XML config
            $component['config'] = array_merge($config, [
                'initialTerms' => $this->fetchPopularTerms((int)($config['number_of_terms'] ?? 0)),
                'searchResultUrl' => $this->_urlBuilder->getUrl('catalogsearch/result/')
            ]);
        }

        return (string)$this->serializer->serialize($layout);
    }

    /**
     * Checks if the module functionality is enabled globally.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * Prevents block rendering if the module is disabled.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Resolves the terms limit and fetches data from the provider.
     *
     * Priority: XML Argument > System Config > Default Constant.
     *
     * @param int $xmlLimit Limit provided in the jsLayout/config XML.
     * @return array Array of search terms data.
     */
    private function fetchPopularTerms(int $xmlLimit): array
    {
        $limit = $xmlLimit ?: $this->config->getNumberOfTerms() ?: self::DEFAULT_TERMS_LIMIT;

        return $this->popularTermsProvider->getPopularTerms(
            null, 
            (int)$limit
        );
    }
}
