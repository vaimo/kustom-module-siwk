<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model;

use Klarna\Siwk\Api\Database\Token\RepositoryInterface;
use Klarna\Siwk\Model\Authentication\Service as AuthenticationService;
use Klarna\Siwk\Model\Authentication\Token\Container;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @internal
 */
class Service
{
    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $repository;
    /**
     * @var AuthenticationService
     */
    private AuthenticationService $authenticationService;
    /**
     * @var Container
     */
    private Container $container;

    /**
     * @param RepositoryInterface $repository
     * @param AuthenticationService $authenticationService
     * @param Container $container
     * @codeCoverageIgnore
     */
    public function __construct(
        RepositoryInterface $repository,
        AuthenticationService $authenticationService,
        Container $container
    ) {
        $this->repository = $repository;
        $this->authenticationService = $authenticationService;
        $this->container = $container;
    }

    /**
     * Getting back the access token
     *
     * @param string $customerId
     * @param StoreInterface $store
     * @return Container
     */
    public function getAccessToken(string $customerId, StoreInterface $store): Container
    {
        if (!$this->repository->existEntryByCustomerId($customerId)) {
            return $this->container;
        }

        $tokenModel = $this->repository->getByCustomerId($customerId);
        if (!$tokenModel->isAccessTokenExpired() && $tokenModel->isAccessTokenUsableForPurchase()) {
            $this->container->setAccessToken($tokenModel->getAccessToken());
            return $this->container;
        }

        if ($tokenModel->isRefreshTokenExpired()) {
            return $this->container;
        }

        try {
            $accessToken = $this->authenticationService->requestNewAccessToken($tokenModel, $store);
        } catch (AuthenticationException $e) {
            return $this->container;
        }

        $this->container->setAccessToken($accessToken);
        return $this->container;
    }

    /**
     * Marking the access token as used for the customer
     *
     * @param string $customerId
     */
    public function markAccessTokenAsUsedByCustomerId(string $customerId): void
    {
        if (!$this->repository->existEntryByCustomerId($customerId)) {
            return;
        }

        $tokenModel = $this->repository->getByCustomerId($customerId);
        if (!$tokenModel->isAccessTokenUsableForPurchase()) {
            return;
        }

        $tokenModel->markAccessTokenAsUsed();
        $this->repository->save($tokenModel);
    }
}
