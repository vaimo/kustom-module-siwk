<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Api;

/**
 * @internal
 */
class Url
{

    /**
     * Getting back the target url used for the API endpoints
     *
     * @param string $environment
     * @return string
     */
    public function getTargetUrl(string $environment): string
    {
        if ($environment === 'production') {
            return 'https://login.klarna.com/';
        }

        return 'https://login.playground.klarna.com/';
    }
}
