<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Storage\Customer;

use Klarna\Siwk\Api\Database\Customer\ModelInterface;
use Klarna\Siwk\Api\Database\Customer\RepositoryInterface as CustomerRepositoryInterface;
use Klarna\Siwk\Model\Database\Customer\ModelFactory;

/**
 * @internal
 */
class Fetch
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $repository;
    /**
     * @var ModelFactory
     */
    private ModelFactory $customerFactory;

    /**
     * @param CustomerRepositoryInterface $repository
     * @param ModelFactory $customerFactory
     * @codeCoverageIgnore
     */
    public function __construct(CustomerRepositoryInterface $repository, ModelFactory $customerFactory)
    {
        $this->repository = $repository;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Getting back the instance based on the Klarna customer ID
     *
     * @param string $customerId
     * @return ModelInterface
     */
    public function getCustomerEntryByKlarnaCustomerId(string $customerId): ModelInterface
    {
        if ($this->repository->existEntryByKlarnaCustomerId($customerId)) {
            return $this->repository->getByKlarnaCustomerId($customerId);
        }

        return $this->customerFactory->create(['data' => ['klarna_customer_id' => $customerId]]);
    }
}
