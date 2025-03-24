<?php
/**
 * Amadeco PopularSearchTerms Module
 *
 * @category   Amadeco
 * @package    Amadeco_PopularSearchTerms
 * @author     Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\PopularSearchTerms\Api;

/**
 * Popular Terms Provider Interface
 */
interface PopularTermsProviderInterface
{
    /**
     * Get popular search terms
     *
     * @param int|null $storeId
     * @return array<int, array{query_text: string, popularity: int, updated_at: string}>
     */
    public function getPopularTerms(?int $storeId = null): array;
}