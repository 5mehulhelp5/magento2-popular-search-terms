<?php
/**
 * Amadeco PopularSearchTerms Module
 *
 * @category   Amadeco
 * @package    Amadeco_PopularSearchTerms
 * @author     Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\PopularSearchTerms\Model;

use Amadeco\PopularSearchTerms\Api\PopularTermsProviderInterface;
use Amadeco\PopularSearchTerms\Helper\Config;
use Amadeco\PopularSearchTerms\Model\Config\Source\SortOrder;
use Magento\Search\Model\Query;
use Magento\Search\Model\ResourceModel\Query\Collection as QueryCollection;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Popular Terms Provider
 */
class PopularTermsProvider implements PopularTermsProviderInterface
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var QueryCollectionFactory
     */
    private QueryCollectionFactory $queryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param Config $config
     * @param QueryCollectionFactory $queryCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        QueryCollectionFactory $queryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->queryCollectionFactory = $queryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get popular search terms
     *
     * @param int|null $storeId
     * @return array<int, array{query_text: string, popularity: int, updated_at: string}>
     */
    public function getPopularTerms(?int $storeId = null): array
    {
        if (!$this->config->isEnabled($storeId)) {
            return [];
        }

        if ($storeId === null) {
            $storeId = (int)$this->storeManager->getStore()->getId();
        }

        /** @var QueryCollection $collection */
        $collection = $this->queryCollectionFactory->create();

        // Utiliser la méthode native pour configurer la collection
        $collection->setPopularQueryFilter($storeId);

        // Ajouter un filtre sur la période si nécessaire
        $timePeriod = $this->config->getTimePeriod($storeId);
        if ($timePeriod > 0) {
            $dateLimit = date('Y-m-d H:i:s', strtotime("-$timePeriod days"));
            $collection->addFieldToFilter('updated_at', ['gt' => $dateLimit]);
        }

        // Si le tri n'est pas par popularité mais par date
        if ($this->config->getSortOrder($storeId) === SortOrder::SORT_BY_RECENCY) {
            $collection->setRecentQueryFilter();
        }

        // Limiter au nombre configuré
        $collection->setPageSize($this->config->getNumberOfTerms($storeId));

        $result = [];
        /** @var Query $item */
        foreach ($collection as $item) {
            $result[] = [
                'query_text' => (string)$item->getQueryText(),
                'popularity' => (int)$item->getPopularity(),
                'updated_at' => (string)$item->getUpdatedAt()
            ];
        }

        return $result;
    }
}