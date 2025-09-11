<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Token\Validation;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @internal
 */
class SignatureValidator
{
    /**
     * @var JWK
     */
    private JWK $jwk;
    /**
     * @var JWT
     */
    private JWT $jwt;

    /**
     * @param JWK $jwk
     * @param JWT $jwt
     * @codeCoverageIgnore
     */
    public function __construct(JWK $jwk, JWT $jwt)
    {
        $this->jwk = $jwk;
        $this->jwt = $jwt;
    }

    /**
     * Returns true if the signature is valid
     *
     * @param array $signingKey
     * @param string $jwt
     * @return bool
     * @throws AuthenticationException
     */
    public function isSignatureValid(array $signingKey, string $jwt): bool
    {
        try {
            $key = $this->jwk::parseKey($signingKey, $signingKey['alg']);
            $this->jwt::decode(
                $jwt,
                $key
            );
        } catch (SignatureInvalidException $e) {
            throw new AuthenticationException(__('Invalid signature.'));
        }

        return true;
    }
}
