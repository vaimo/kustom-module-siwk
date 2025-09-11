<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Api\Endpoints;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Siwk\Api\Endpoints\OpenIdConfigurationInterface;
use Magento\Framework\Exception\AuthenticationException;
use Klarna\Siwk\Model\Authentication\Api\Url;

/**
 * @api
 */
class OpenIdConfiguration implements OpenIdConfigurationInterface
{
    /**
     * @var Client
     */
    private Client $client;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var Url
     */
    private Url $url;

    /**
     * @param Client $client
     * @param LoggerInterface $logger
     * @param Url $url
     * @codeCoverageIgnore
     */
    public function __construct(Client $client, LoggerInterface $logger, Url $url)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $region, string $environment): array
    {
        $url = $this->url->getTargetUrl($environment) . $region . '/lp/idp/.well-known/openid-configuration';

        try {
            $response = $this->client->get($url);
        } catch (GuzzleException $e) {
            $this->logger->info('Open ID configuration request failed. Reason: ' . $e->getMessage());
            throw new AuthenticationException(__('API request to get back the open ID configuration has failed'));
        }

        return json_decode($response->getBody()->__toString(), true);
    }
}
