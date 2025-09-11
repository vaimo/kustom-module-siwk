<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication;

use Klarna\AdminSettings\Model\Configurations\Api;
use Klarna\Siwk\Api\Endpoints\TokenRegenerationInterface;
use Klarna\Siwk\Model\Authentication\Storage\Customer\Update as CustomerUpdate;
use Klarna\Siwk\Model\Authentication\Storage\Token\Update as TokenUpdate;
use Klarna\Siwk\Model\Authentication\Account\Service as AccountService;
use Klarna\Siwk\Model\Authentication\Token\Validation\MainValidator;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\AuthenticationException;
use Klarna\Siwk\Api\Database\Token\ModelInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @internal
 */
class Service
{
    /**
     * @var TokenUpdate
     */
    private TokenUpdate $tokenUpdate;
    /**
     * @var AccountService
     */
    private AccountService $accountService;
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;
    /**
     * @var MainValidator
     */
    private MainValidator $validator;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var TokenRegenerationInterface
     */
    private TokenRegenerationInterface $tokenRegeneration;
    /**
     * @var CustomerUpdate
     */
    private CustomerUpdate $customerUpdate;
    /**
     * @var Api
     */
    private Api $apiConfiguration;

    /**
     * @param TokenUpdate $tokenUpdate
     * @param AccountService $accountService
     * @param CustomerSession $customerSession
     * @param MainValidator $validator
     * @param StoreManagerInterface $storeManager
     * @param TokenRegenerationInterface $tokenRegeneration
     * @param CustomerUpdate $customerUpdate
     * @param Api $apiConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        TokenUpdate $tokenUpdate,
        AccountService $accountService,
        CustomerSession $customerSession,
        MainValidator $validator,
        StoreManagerInterface $storeManager,
        TokenRegenerationInterface $tokenRegeneration,
        CustomerUpdate $customerUpdate,
        Api $apiConfiguration
    ) {
        $this->tokenUpdate = $tokenUpdate;
        $this->accountService = $accountService;
        $this->customerSession = $customerSession;
        $this->validator = $validator;
        $this->storeManager = $storeManager;
        $this->tokenRegeneration = $tokenRegeneration;
        $this->customerUpdate = $customerUpdate;
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * Logging the user into the account
     *
     * @param string $refreshToken
     * @param string $idToken
     * @param string $klarnaCustomerId
     */
    public function login(
        string $refreshToken,
        string $idToken,
        string $klarnaCustomerId
    ): void {
        if ($this->customerSession->isLoggedIn()) {
            throw new AuthenticationException(__('Already logged in to a account'));
        }
        $this->validator->validateToken($idToken, $this->storeManager->getStore());

        $customer = $this->accountService->saveCustomerByIdToken($idToken);
        $this->tokenUpdate->createTokenEntry(
            $refreshToken,
            $idToken,
            (string) $customer->getId()
        );
        $this->customerUpdate->createCustomerEntry((string) $customer->getId(), $klarnaCustomerId);
    }

    /**
     * Getting back a new request access token
     *
     * @param ModelInterface $tokenModel
     * @param StoreInterface $store
     * @return string
     */
    public function requestNewAccessToken(ModelInterface $tokenModel, StoreInterface $store): string
    {
        $clientId = $this->apiConfiguration->getClientIdentifier($store, $store->getCurrentCurrencyCode());
        $region = $this->apiConfiguration->getRegion($store, $store->getCurrentCurrencyCode());
        $environment = $this->apiConfiguration->getMode($store, $store->getCurrentCurrencyCode());

        $apiResponse = $this->tokenRegeneration->execute(
            $region,
            $environment,
            $tokenModel->getRefreshToken(),
            $clientId
        );
        $this->validator->validateToken($apiResponse['id_token'], $this->storeManager->getStore());

        $tokenModel = $this->tokenUpdate->saveTokenEntryWithNewData(
            $apiResponse['access_token'],
            $apiResponse['expires_in'],
            $apiResponse['refresh_token'],
            $apiResponse['id_token'],
            $tokenModel
        );

        return $tokenModel->getAccessToken();
    }
}
