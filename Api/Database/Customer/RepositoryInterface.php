<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Api\Database\Customer;

use Magento\Framework\Model\AbstractModel;

/**
 * @api
 */
interface RepositoryInterface
{
    /**
     * Saving the instance
     *
     * @param AbstractModel $model
     * @return AbstractModel
     */
    public function save(AbstractModel $model): AbstractModel;

    /**
     * Returns true if there is a entry for the Klarna customer ID
     *
     * @param string $customerId
     * @return bool
     */
    public function existEntryByKlarnaCustomerId(string $customerId): bool;

    /**
     * Getting back the instance by the Klarna customer ID
     *
     * @param string $customerId
     * @return ModelInterface
     */
    public function getByKlarnaCustomerId(string $customerId): ModelInterface;
}
