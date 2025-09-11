<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Api\Database\Customer;

/**
 * @api
 */
interface ModelInterface
{
    /**
     * Setting the shop customer ID
     *
     * @param string $customerId
     * @return $this
     */
    public function setShopCustomerId(string $customerId): self;

    /**
     * Setting the Klarna customer ID
     *
     * @param string $customerId
     * @return $this
     */
    public function setKlarnaCustomerId(string $customerId): self;
}
