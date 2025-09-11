<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Token;

/**
 * @internal
 */
class Container
{
    /**
     * @var string
     */
    private string $accessToken = '';

    /**
     * Setting the access token
     *
     * @param string $accessToken
     * @return $this
     */
    public function setAccessToken(string $accessToken): Container
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * Getting back the access token
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Returns true if the access token exists
     *
     * @return bool
     */
    public function hasAccessToken(): bool
    {
        return $this->accessToken !== '';
    }
}
