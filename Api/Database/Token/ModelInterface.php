<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Api\Database\Token;

/**
 * @api
 */
interface ModelInterface
{

    /**
     * Setting the access token
     *
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): self;

    /**
     * Getting back the access token
     *
     * @return string
     */
    public function getAccessToken(): ?string;

    /**
     * Marking the access token as used
     *
     * @return $this
     */
    public function markAccessTokenAsUsed(): self;

    /**
     * Marking the access token as unused
     *
     * @return $this
     */
    public function markAccessTokenAsUnUsed(): self;

    /**
     * Returns true if the access token was already used for a purchase
     *
     * @return bool
     */
    public function isAccessTokenUsableForPurchase(): bool;

    /**
     * Setting the refresh token
     *
     * @param string $refreshToken
     */
    public function setRefreshToken(string $refreshToken): self;

    /**
     * Getting back the refresh token
     *
     * @return string
     */
    public function getRefreshToken(): string;

    /**
     * Setting the ID token
     *
     * @param string $idToken
     */
    public function setIdToken(string $idToken): self;

    /**
     * Getting back the ID token
     *
     * @return string
     */
    public function getIdToken(): string;

    /**
     * Setting the access token expiration number
     *
     * @param int $expirationNumber
     * @return $this
     */
    public function setAccessTokenExpirationNumber(int $expirationNumber): self;

    /**
     * Getting back the access token expiration number
     *
     * @return int|null
     */
    public function getAccessTokenExpirationNumber(): ?int;

    /**
     * Returns true if the access token is not expired
     *
     * @return bool
     */
    public function isAccessTokenExpired(): bool;

    /**
     * Setting the refresh token expiration number
     *
     * @param int $expirationNumber
     * @return $this
     */
    public function setRefreshTokenExpirationNumber(int $expirationNumber): self;

    /**
     * Getting back the refresh token expiration number
     *
     * @return int
     */
    public function getRefreshTokenExpirationNumber(): int;

    /**
     * Returns true if the refresh token is not expired
     *
     * @return bool
     */
    public function isRefreshTokenExpired(): bool;

    /**
     * Getting back the time as string when the database row was created
     *
     * @return string
     */
    public function getCreatedAtTime(): string;

    /**
     * Getting back the time as string when the database row was updated the last time
     *
     * @return string
     */
    public function getUpdatedAtTime(): string;

    /**
     * Setting the customer ID
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId(string $customerId): self;

    /**
     * Getting back the customer ID
     *
     * @return string|null
     */
    public function getCustomerId(): ?string;
}
