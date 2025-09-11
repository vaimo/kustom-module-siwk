<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Token\Validation;

use Klarna\AdminSettings\Model\Configurations\Api;
use Magento\Store\Api\Data\StoreInterface;
use Klarna\Siwk\Model\Authentication\Token\Decoder;

/**
 * @internal
 */
class MainValidator
{
    /**
     * @var Decoder
     */
    private Decoder $decoder;
    /**
     * @var ClaimValidator
     */
    private ClaimValidator $claimValidator;
    /**
     * @var SignatureValidator
     */
    private SignatureValidator $signatureValidator;
    /**
     * @var SigningKeyValidator
     */
    private SigningKeyValidator $signingKeyValidator;
    /**
     * @var Api
     */
    private Api $apiConfiguration;

    /**
     * @param Decoder $decoder
     * @param ClaimValidator $claimValidator
     * @param SignatureValidator $signatureValidator
     * @param SigningKeyValidator $signingKeyValidator
     * @param Api $apiConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        Decoder $decoder,
        ClaimValidator $claimValidator,
        SignatureValidator $signatureValidator,
        SigningKeyValidator $signingKeyValidator,
        Api $apiConfiguration
    ) {
        $this->decoder = $decoder;
        $this->claimValidator = $claimValidator;
        $this->signatureValidator = $signatureValidator;
        $this->signingKeyValidator = $signingKeyValidator;
        $this->apiConfiguration = $apiConfiguration;
    }

    /**
     * Validating the token
     *
     * @param string $token
     * @param StoreInterface $store
     */
    public function validateToken(string $token, StoreInterface $store): void
    {
        $region = $this->apiConfiguration->getRegion($store, $store->getCurrentCurrencyCode());
        $environment = $this->apiConfiguration->getMode($store, $store->getCurrentCurrencyCode());

        $this->decoder->calculate($token);
        $accessTokenHeader = $this->decoder->getHeader();

        $signingKey = $this->signingKeyValidator->existTargetSigningKey(
            $region,
            $environment,
            $accessTokenHeader['kid']
        );
        $this->signatureValidator->isSignatureValid($signingKey, $token);
        $this->claimValidator->isClaimValid($region, $environment, $this->decoder->getPayLoad());
    }
}
