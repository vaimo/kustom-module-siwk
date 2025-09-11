<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Storage\Token;

use Klarna\Siwk\Api\Database\Token\RepositoryInterface as TokenRepositoryInterface;
use Klarna\Siwk\Api\Database\Token\ModelInterface;

/**
 * @internal
 */
class Update
{
    /**
     * @var Fetch
     */
    private Fetch $fetch;
    /**
     * @var TokenRepositoryInterface
     */
    private TokenRepositoryInterface $tokenRepository;

    /**
     * @param Fetch $fetch
     * @param TokenRepositoryInterface $tokenRepository
     * @codeCoverageIgnore
     */
    public function __construct(Fetch $fetch, TokenRepositoryInterface $tokenRepository)
    {
        $this->fetch = $fetch;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Updating the token model instance with new data
     *
     * @param string $accessToken
     * @param int $accessTokenExpirationNumber
     * @param string $refreshToken
     * @param string $idToken
     * @param ModelInterface $tokenModel
     * @return ModelInterface
     */
    public function saveTokenEntryWithNewData(
        string $accessToken,
        int $accessTokenExpirationNumber,
        string $refreshToken,
        string $idToken,
        ModelInterface $tokenModel
    ): ModelInterface {
        $tokenModel->setAccessToken($accessToken);
        $tokenModel->setAccessTokenExpirationNumber($accessTokenExpirationNumber);
        $tokenModel->setRefreshToken($refreshToken);
        $tokenModel->setIdToken($idToken);
        $tokenModel->setRefreshTokenExpirationNumber(5184000);
        $tokenModel->markAccessTokenAsUnUsed();

        return $this->tokenRepository->save($tokenModel);
    }

    /**
     * Creating a new SIWK token entry
     *
     * @param string $refreshToken
     * @param string $idToken
     * @param string $customerId
     * @return ModelInterface
     */
    public function createTokenEntry(string $refreshToken, string $idToken, string $customerId): ModelInterface
    {
        $tokenModel = $this->fetch->getTokenEntryByShopCustomerId($customerId);
        $tokenModel->setRefreshToken($refreshToken);
        $tokenModel->setIdToken($idToken);
        $tokenModel->setRefreshTokenExpirationNumber(5184000);
        $tokenModel->markAccessTokenAsUnUsed();

        return $this->tokenRepository->save($tokenModel);
    }
}
