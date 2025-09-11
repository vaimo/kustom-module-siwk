<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Account;

use Magento\Customer\Model\Data\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
class Customer
{
    /**
     * @var CustomerFactory
     */
    private CustomerFactory $customerFactory;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    private const DOB_TOTAL_NUMBERS = 3;

    /**
     * @param CustomerFactory $customerFactory
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Getting the customer by creating it or  returning a existing one
     *
     * @param array $data
     * @return CustomerInterface
     */
    public function get(array $data): CustomerInterface
    {
        try {
            $customer = $this->customerRepository->get($data['email']);
        } catch (NoSuchEntityException $e) {
            $customer = $this->customerFactory->create();
        }

        $storeId = $this->storeManager->getStore()->getId();
        $websiteId = $this->storeManager->getWebsite()->getId();

        $customer->setWebsiteId($websiteId);
        $customer->setStoreId($storeId);

        $customer->setEmail($data['email']);
        $customer->setFirstname($data['given_name']);
        $customer->setLastname($data['family_name']);

        $this->setDobField($data, $customer);

        return $this->customerRepository->save($customer);
    }

    /**
     * Setting the DOB field if it exists in the data
     *
     * @param array $data
     * @param CustomerInterface $customer
     * @return void
     */
    private function setDobField(array $data, CustomerInterface $customer): void
    {
        if (isset($data['date_of_birth'])) {
            $exploded = explode('-', $data['date_of_birth']);
            if (count($exploded) !== self::DOB_TOTAL_NUMBERS) {
                return;
            }

            if (!checkdate((int)$exploded[1], (int)$exploded[2], (int)$exploded[0])) {
                return;
            }

            $customer->setDob($data['date_of_birth']);
        }
    }
}
