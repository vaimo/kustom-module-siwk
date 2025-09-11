<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Database\Token;

use Klarna\Siwk\Api\Database\Token\ModelInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * @internal
 */
class Model extends AbstractModel implements ModelInterface
{

    public const CACHE_TAG = 'klarna_siwk_token';

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken(string $accessToken): ModelInterface
    {
        $this->setData('access_token', $this->encryptor->encrypt($accessToken));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken(): ?string
    {
        return $this->encryptor->decrypt($this->_getData('access_token'));
    }

    /**
     * @inheritDoc
     */
    public function setRefreshToken(string $refreshToken): ModelInterface
    {
        $this->setData('refresh_token', $this->encryptor->encrypt($refreshToken));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRefreshToken(): string
    {
        return $this->encryptor->decrypt($this->_getData('refresh_token'));
    }

    /**
     * @inheritDoc
     */
    public function setIdToken(string $idToken): ModelInterface
    {
        $this->setData('id_token', $this->encryptor->encrypt($idToken));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdToken(): string
    {
        return $this->encryptor->decrypt($this->_getData('id_token'));
    }

    /**
     * @inheritDoc
     */
    public function setAccessTokenExpirationNumber(int $expirationNumber): ModelInterface
    {
        $this->setData('access_token_expires_in', $expirationNumber);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccessTokenExpirationNumber(): ?int
    {
        return (int) $this->_getData('access_token_expires_in');
    }

    /**
     * @inheritDoc
     */
    public function isAccessTokenExpired(): bool
    {
        $number = $this->getAccessTokenExpirationNumber();
        if ($number === null) {
            return true;
        }
        return $this->isExpired($number, $this->getUpdatedAtTime());
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAtTime(): string
    {
        return $this->_getData('created_at');
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAtTime(): string
    {
        return $this->_getData('updated_at');
    }

    /**
     * @inheritDoc
     */
    public function markAccessTokenAsUsed(): ModelInterface
    {
        $this->setData('usable_for_purchase', false);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function markAccessTokenAsUnUsed(): ModelInterface
    {
        $this->setData('usable_for_purchase', true);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAccessTokenUsableForPurchase(): bool
    {
        return (bool) $this->_getData('usable_for_purchase');
    }

    /**
     * @inheritDoc
     */
    public function setRefreshTokenExpirationNumber(int $expirationNumber): ModelInterface
    {
        $this->setData('refresh_token_expires_in', $expirationNumber);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRefreshTokenExpirationNumber(): int
    {
        return (int) $this->_getData('refresh_token_expires_in');
    }

    /**
     * @inheritDoc
     */
    public function isRefreshTokenExpired(): bool
    {
        $number = $this->getRefreshTokenExpirationNumber();
        if ($number === null) {
            return true;
        }

        return $this->isExpired($number, $this->getUpdatedAtTime());
    }

    /**
     * Returns true if the current time is older then the calculated time
     *
     * @param int $expirationNumber
     * @param string $time
     * @return bool
     */
    private function isExpired(int $expirationNumber, string $time): bool
    {
        $updateTimeUnix = strtotime($time);
        $expirationTimeUnix = $expirationNumber + $updateTimeUnix;

        return time() >= $expirationTimeUnix;
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(string $customerId): ModelInterface
    {
        $this->setData('customer_id', $customerId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): ?string
    {
        return $this->_getData('customer_id');
    }

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     * @codingStandardsIgnoreLine
     */
    protected function _construct()
    {
        $this->encryptor = ObjectManager::getInstance()->get(EncryptorInterface::class);

        $this->_init(\Klarna\Siwk\Model\Database\Token\ResourceModel\Siwk::class);
    }
}
