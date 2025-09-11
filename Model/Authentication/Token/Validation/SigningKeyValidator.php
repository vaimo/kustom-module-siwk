<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Token\Validation;

use Klarna\Siwk\Api\Endpoints\JwksInterface;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @internal
 */
class SigningKeyValidator
{
    /**
     * @var JwksInterface
     */
    private JwksInterface $apiJwks;

    /**
     * @param JwksInterface $apiJwks
     * @codeCoverageIgnore
     */
    public function __construct(JwksInterface $apiJwks)
    {
        $this->apiJwks = $apiJwks;
    }

    /**
     * Checks if the target signing key for the jwt kid exists and returns it else throwing an exception
     *
     * @param string $region
     * @param string $environment
     * @param string $jwtKid
     * @return array
     * @throws AuthenticationException
     */
    public function existTargetSigningKey(string $region, string $environment, string $jwtKid): array
    {
        $publicKeys = $this->apiJwks->execute($region, $environment);

        foreach ($publicKeys['keys'] as $keyContent) {
            if (isset($keyContent['kid']) && $jwtKid === $keyContent['kid']) {
                return $keyContent;
            }
        }

        throw new AuthenticationException(__('Access token KID does match the available list of KIDs.'));
    }
}
