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
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\Query;

/**
 * Block for Popular Search Terms UI Component.
 *
 * This block enriches the static JsLayout configuration defined in XML with
 * dynamic search terms and store-specific URLs.
 */
class SearchTerms extends Template implements IdentityInterface
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
            $componentConfig = $component['config'] ?? [];

            // Determine Loading Mode
            $loadMethod = $this->config->getLoadMethod();
            $isAjax = ($loadMethod === LoadMethod::LOADING_AJAX);

            // Initialize variables
            $initialTerms = [];
            $ajaxUrl = '';

            if ($isAjax) {
                // AJAX Mode: Empty terms, Provide URL
                $ajaxUrl = $this->getUrl('amadeco_popularterms/ajax/getterms');
            } else {
                // Direct Mode: Fetch terms, No URL
                $xmlLimit = isset($componentConfig['number_of_terms'])
                    ? (int)$componentConfig['number_of_terms']
                    : null;
                $initialTerms = $this->fetchPopularTerms($xmlLimit);
            }

            // Inject Configuration
            $component['config'] = array_merge($componentConfig, [
                'initialTerms' => $initialTerms,
                'ajaxUrl' => $ajaxUrl,
                'searchResultUrl' => $this->getUrl('catalogsearch/result/')
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
     * @param null|int $xmlLimit Limit provided in the jsLayout/config XML.
     * @return array Array of search terms data.
     */
    private function fetchPopularTerms(?int $xmlLimit = null): array
    {
        $limit = $xmlLimit ?: $this->config->getNumberOfTerms() ?: self::DEFAULT_TERMS_LIMIT;

        return $this->popularTermsProvider->getPopularTerms(
            null,
            (int)$limit
        );
    }

    /**
     * Return identifiers for produced content
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        // This ensures the block clears if Search Terms are updated
        return [Query::CACHE_TAG];
    }
}
