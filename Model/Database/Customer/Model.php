<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Database\Customer;

use Klarna\Siwk\Api\Database\Customer\ModelInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * @internal
 */
class Model extends AbstractModel implements ModelInterface
{

    public const CACHE_TAG = 'klarna_siwk_customer';

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function setShopCustomerId(string $customerId): ModelInterface
    {
        $this->setData('shop_customer_id', $customerId);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setKlarnaCustomerId(string $customerId): ModelInterface
    {
        $this->setData('klarna_customer_id', $customerId);
        return $this;
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->_init(\Klarna\Siwk\Model\Database\Customer\ResourceModel\Model::class);
    }
}
