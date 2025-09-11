<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Controller\Klarna;

use Klarna\Base\Controller\CsrfAbstract;
use Klarna\Logger\Api\LoggerInterface;
use Klarna\Siwk\Model\Authentication\Service;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Klarna\Base\Model\Responder\Result;
use Magento\Framework\Exception\AuthenticationException;

/**
 * @api
 */
class Login extends CsrfAbstract implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var Service
     */
    private Service $service;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param RequestInterface $request
     * @param Result $result
     * @param Service $service
     * @param LoggerInterface $logger
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface $request,
        Result $result,
        Service $service,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->result = $result;
        $this->service = $service;
        $this->logger = $logger;
    }

    /**
     * Logging in the user
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $rawParameter = json_decode($this->request->getContent(), true);
        $httpCode = 204;
        $result = [];

        try {
            $this->logger->info('Logging in the user for ID token: ' . $rawParameter['id_token']);
            $this->service->login(
                $rawParameter['refresh_token'],
                $rawParameter['id_token'],
                $rawParameter['klarna_customer_id']
            );
        } catch (AuthenticationException $e) {
            $this->logger->info(
                'Logging in the user failed for ID token: ' .
                $rawParameter['id_token'] .
                '. Reason: ' .
                $e->getMessage()
            );

            $httpCode = 400;
            $result['error_message'] = $e->getMessage();
        }

        $result['status'] = $httpCode;
        return $this->result->getJsonResult($httpCode, $result);
    }
}
