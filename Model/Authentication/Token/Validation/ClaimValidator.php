<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Token\Validation;

use Klarna\Siwk\Api\Endpoints\OpenIdConfigurationInterface;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @internal
 */
class ClaimValidator
{
    /**
     * @var OpenIdConfigurationInterface
     */
    private OpenIdConfigurationInterface $apiOpenIdConfiguration;

    /**
     * @param OpenIdConfigurationInterface $apiOpenIdConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(OpenIdConfigurationInterface $apiOpenIdConfiguration)
    {
        $this->apiOpenIdConfiguration = $apiOpenIdConfiguration;
    }

    /**
     * Returns true if the claim is valid
     *
     * @param string $region
     * @param string $environment
     * @param array $payLoad
     * @return bool
     * @throws AuthenticationException
     */
    public function isClaimValid(string $region, string $environment, array $payLoad): bool
    {
        $openIdConfig = $this->apiOpenIdConfiguration->execute($region, $environment);

        if ($payLoad['iss'] !== $openIdConfig['issuer']) {
            throw new AuthenticationException(__('Wrong issuer.'));
        }

        return true;
    }
}
