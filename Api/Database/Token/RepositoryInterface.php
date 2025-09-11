<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Api\Database\Token;

use Magento\Framework\Model\AbstractModel;

/**
 * @api
 */
interface RepositoryInterface
{

    /**
     * Getting back the instance by customer ID
     *
     * @param string $customerId
     * @return $this
     */
    public function getByCustomerId(string $customerId): ModelInterface;

    /**
     * Returns true if an entry for the given customer ID exists.
     *
     * @param string $customerId
     * @return bool
     */
    public function existEntryByCustomerId(string $customerId): bool;

    /**
     * Saving the instance
     *
     * @param AbstractModel $customerModel
     * @return AbstractModel
     */
    public function save(AbstractModel $customerModel): AbstractModel;
}
