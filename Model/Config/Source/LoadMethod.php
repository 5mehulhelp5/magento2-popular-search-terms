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

class LoadMethod implements OptionSourceInterface
{
    public const LOADING_DIRECT = 'direct';
    public const LOADING_AJAX = 'ajax';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::LOADING_DIRECT, 'label' => __('Direct Injection (HTML Head)')],
            ['value' => self::LOADING_AJAX, 'label' => __('AJAX Loading (Async)')]
        ];
    }
}
