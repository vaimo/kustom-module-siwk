<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Block;

use Klarna\AdminSettings\Model\Configurations\Siwk;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * @internal
 */
abstract class Base extends Template
{
    /**
     * @var Siwk
     */
    private Siwk $siwkConfiguration;

    /**
     * @param Siwk $siwkConfiguration
     * @param Context $context
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(Siwk $siwkConfiguration, Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->siwkConfiguration = $siwkConfiguration;
    }

    /**
     * Returns true if SIWK is in general enabled
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isSiwkEnabled(): bool
    {
        return $this->siwkConfiguration->isEnabled($this->_storeManager->getStore());
    }
}
