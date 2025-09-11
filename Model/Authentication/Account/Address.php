<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Account;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * @internal
 */
class Address
{
    /**
     * @var AddressInterfaceFactory
     */
    private AddressInterfaceFactory $factory;
    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $repository;

    /**
     * @param AddressInterfaceFactory $factory
     * @param AddressRepositoryInterface $repository
     * @codeCoverageIgnore
     */
    public function __construct(AddressInterfaceFactory $factory, AddressRepositoryInterface $repository)
    {
        $this->factory = $factory;
        $this->repository = $repository;
    }

    /**
     * Adding the address to the customer
     *
     * @param CustomerInterface $customer
     * @param array $data
     * @return CustomerInterface
     */
    public function add(CustomerInterface $customer, array $data): CustomerInterface
    {
        $billingAddress = $data['billing_address'];

        $address = $this->factory->create();
        $address->setCountryId($billingAddress['country']);
        $address->setFirstname($data['given_name']);
        $address->setLastname($data['family_name']);
        $address->setTelephone($data['phone']);
        $address->setRegionId($billingAddress['region']);
        $address->setCity($billingAddress['city']);
        $address->setPostcode($billingAddress['postal_code']);
        $address->setCustomerId($customer->getId());
        $address->setStreet([$billingAddress['street_address']]);

        if (empty($customer->getAddresses())) {
            $address->setIsDefaultShipping(true);
            $address->setIsDefaultBilling(true);
        }

        $this->repository->save($address);

        return $customer;
    }

    /**
     * Returns true if the address already exists in the address book
     *
     * @param CustomerInterface $customer
     * @param array $klarnaData
     * @return bool
     */
    public function existInAddressBook(CustomerInterface $customer, array $klarnaData): bool
    {
        $fields = [
            'given_name' => 'firstname',
            'family_name' => 'lastname',
            'phone' => 'telephone',
            'billing_address' => [
                'country' => 'country_id',
                'region' => 'region_id',
                'city' => 'city',
                'postal_code' => 'postcode',
                'street_address' => 'street'
            ]
        ];

        if (count($customer->getAddresses()) === 0) {
            return false;
        }

        foreach ($customer->getAddresses() as $address) {
            $customerAddressData = $address->__toArray();
            $customerAddressData['street'] = implode(' ', $customerAddressData['street']);

            foreach ($fields as $topFieldKey => $topFieldValue) {
                if (is_array($topFieldValue)) {
                    foreach ($topFieldValue as $innerFieldKey => $innerFieldValue) {
                        if ($this->isDifferentValue(
                            $customerAddressData[$innerFieldValue],
                            $klarnaData[$topFieldKey][$innerFieldKey]
                        )) {
                            continue 3;
                        }
                    }
                    continue;
                }

                if ($this->isDifferentValue($customerAddressData[$topFieldValue], $klarnaData[$topFieldKey])) {
                    continue 2;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Returns true if the value is different
     *
     * @param string|null $customerAddressValue
     * @param string|null $klarnaAddressValue
     * @return bool
     */
    private function isDifferentValue(?string $customerAddressValue, ?string $klarnaAddressValue): bool
    {
        $customerAddressValue = $this->normalise($customerAddressValue);
        $klarnaAddressValue = $this->normalise($klarnaAddressValue);

        return $klarnaAddressValue !== null &&
            $klarnaAddressValue !== $customerAddressValue;
    }

    /**
     * Normalising the value
     *
     * @param string|null $value
     * @return string|null
     */
    private function normalise(?string $value): ?string
    {
        if ($value === null) {
            return $value;
        }

        $value = strtolower($value);
        return trim($value);
    }
}
