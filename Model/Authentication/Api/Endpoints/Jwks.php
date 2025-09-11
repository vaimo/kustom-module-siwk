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
use Klarna\Siwk\Api\Endpoints\JwksInterface;
use Klarna\Siwk\Model\Authentication\Api\Url;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @api
 */
class Jwks implements JwksInterface
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
        $url = $this->url->getTargetUrl($environment) . $region . '/lp/idp/.well-known/jwks.json';

        try {
            $response = $this->client->get($url);
        } catch (GuzzleException $e) {
            $this->logger->info('Jwks request failed. Reason: ' . $e->getMessage());
            throw new AuthenticationException(__('API request to collect jwks keys has failed.'));
        }

        return json_decode($response->getBody()->__toString(), true);
    }
}
