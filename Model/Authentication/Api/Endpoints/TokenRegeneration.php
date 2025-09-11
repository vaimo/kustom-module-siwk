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
use Klarna\Siwk\Api\Endpoints\TokenRegenerationInterface;
use Klarna\Siwk\Model\Authentication\Api\Url;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @api
 */
class TokenRegeneration implements TokenRegenerationInterface
{

    /**
     * @var Client
     */
    private Client $client;
    /**
     * @var Url
     */
    private Url $url;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Client $client
     * @param LoggerInterface $logger
     * @param Url $url
     * @codeCoverageIgnore
     */
    public function __construct(Client $client, LoggerInterface $logger, Url $url)
    {
        $this->client = $client;
        $this->url = $url;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $region, string $environment, string $refreshToken, string $clientId): array
    {
        $fullUrl = $this->url->getTargetUrl($environment) . $region . '/lp/idp/oauth2/token';

        $requestData = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $clientId
            ]
        ];
        try {
            $response = $this->client->post($fullUrl, $requestData);
        } catch (GuzzleException $e) {
            $this->logger->info(
                'Token regeneration request failed for refresh token ' .
                $refreshToken .
                '. Reason: ' .
                $e->getMessage()
            );
            throw new AuthenticationException(__('API request to regenerate the token has failed'));
        }

        return json_decode($response->getBody()->__toString(), true);
    }
}
