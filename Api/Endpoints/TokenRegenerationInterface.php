<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Api\Endpoints;

/**
 * @api
 */
interface TokenRegenerationInterface
{

    /**
     * Regenerating the token
     *
     * @param string $region
     * @param string $environment
     * @param string $refreshToken
     * @param string $clientId
     * @return array
     */
    public function execute(string $region, string $environment, string $refreshToken, string $clientId): array;
}
