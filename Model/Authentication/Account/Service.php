<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Account;

use Klarna\Siwk\Model\Authentication\Token\Decoder;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\CustomerRegistry;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class Service
{
    /**
     * @var Customer
     */
    private Customer $customer;
    /**
     * @var Decoder
     */
    private Decoder $tokenDecoder;
    /**
     * @var Address
     */
    private Address $address;
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;
    /**
     * @var CustomerRegistry
     */
    private CustomerRegistry $customerRegistry;

    /**
     * @param Customer $customer
     * @param Decoder $tokenDecoder
     * @param Address $address
     * @param CustomerSession $customerSession
     * @param CustomerRegistry $customerRegistry
     * @codeCoverageIgnore
     */
    public function __construct(
        Customer $customer,
        Decoder $tokenDecoder,
        Address $address,
        CustomerSession $customerSession,
        CustomerRegistry $customerRegistry
    ) {
        $this->customer = $customer;
        $this->tokenDecoder = $tokenDecoder;
        $this->address = $address;
        $this->customerSession = $customerSession;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * Creating the customer based on the content of the ID token
     *
     * @param string $idToken
     * @return CustomerInterface
     */
    public function saveCustomerByIdToken(string $idToken): CustomerInterface
    {
        $this->tokenDecoder->calculate($idToken);
        $payLoad = $this->tokenDecoder->getPayLoad();
        $customer = $this->customer->get($payLoad);

        if (!$this->address->existInAddressBook($customer, $payLoad)) {
            $this->address->add($customer, $payLoad);
        }

        $customerRegistry = $this->customerRegistry->retrieveByEmail($customer->getEmail());

        $this->customerSession->setCustomerAsLoggedIn($customerRegistry);
        $this->customerSession->regenerateId();

        return $customer;
    }
}
