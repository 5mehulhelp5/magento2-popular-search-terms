<?php
/**
 * Amadeco PopularSearchTerms Module
 *
 * @category   Amadeco
 * @package    Amadeco_PopularSearchTerms
 * @author     Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\PopularSearchTerms\Controller\Ajax;

use Amadeco\PopularSearchTerms\Api\PopularTermsProviderInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * AJAX controller for getting popular search terms
 */
class GetTerms implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var PopularTermsProviderInterface
     */
    private PopularTermsProviderInterface $popularTermsProvider;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param PopularTermsProviderInterface $popularTermsProvider
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        PopularTermsProviderInterface $popularTermsProvider,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->popularTermsProvider = $popularTermsProvider;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Execute action to get popular search terms
     *
     * @return Json
     */
    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();

        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
            $terms = $this->popularTermsProvider->getPopularTerms($storeId);

            return $result->setData(['success' => true, 'terms' => $terms]);
        } catch (NoSuchEntityException | LocalizedException $e) {
            $this->logger->error($e->getMessage());

            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->logger->critical($e);

            return $result->setData(['success' => false, 'message' => __('An error occurred while retrieving popular search terms.')]);
        }
    }
}