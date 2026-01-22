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
 * Load Method Source Model
 */
class LoadMethod implements OptionSourceInterface
{
    /**
     * Load by direct injection
     */
    public const LOADING_DIRECT = 'direct';

    /**
     * Load by AJAX Controller search terms block
     */
    public const LOADING_AJAX = 'ajax';

    /**
     * Get options array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::LOADING_DIRECT, 'label' => __('Direct Injection (HTML Head)')],
            ['value' => self::LOADING_AJAX, 'label' => __('AJAX Loading (Async)')]
        ];
    }
}
