<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\System\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @internal
 */
class Environment implements OptionSourceInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return[
            [
                'value' => 'playground',
                'label' => __('Playground')
            ],
            [
                'value' => 'production',
                'label' => __('Production')
            ]
        ];
    }
}
