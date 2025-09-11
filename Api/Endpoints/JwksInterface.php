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
interface JwksInterface
{

    /**
     * Performing the request to obtain the Klarna keys
     *
     * @param string $region
     * @param string $environment
     */
    public function execute(string $region, string $environment): array;
}
