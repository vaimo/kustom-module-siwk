<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Storage\Customer;

use Klarna\Siwk\Api\Database\Customer\ModelInterface as CustomerInterface;
use Klarna\Siwk\Api\Database\Customer\RepositoryInterface as CustomerRepositoryInterface;

/**
 * @internal
 */
class Update
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;
    /**
     * @var Fetch
     */
    private Fetch $fetch;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param Fetch $fetch
     * @codeCoverageIgnore
     */
    public function __construct(CustomerRepositoryInterface $customerRepository, Fetch $fetch)
    {
        $this->customerRepository = $customerRepository;
        $this->fetch = $fetch;
    }

    /**
     * Saving the customer ID mapping information
     *
     * @param string $shopCustomerId
     * @param string $klarnaCustomerId
     * @return CustomerInterface
     */
    public function createCustomerEntry(string $shopCustomerId, string $klarnaCustomerId): CustomerInterface
    {
        $model = $this->fetch->getCustomerEntryByKlarnaCustomerId($klarnaCustomerId);
        $model->setShopCustomerId($shopCustomerId);

        return $this->customerRepository->save($model);
    }
}
