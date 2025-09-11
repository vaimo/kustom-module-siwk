<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Storage\Token;

use Klarna\Siwk\Api\Database\Token\RepositoryInterface;
use Klarna\Siwk\Api\Database\Token\ModelInterface;
use Klarna\Siwk\Model\Database\Token\ModelFactory;

/**
 * @internal
 */
class Fetch
{
    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $repository;
    /**
     * @var ModelFactory
     */
    private ModelFactory $tokenModelFactory;

    /**
     * @param RepositoryInterface $repository
     * @param ModelFactory $tokenModelFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        RepositoryInterface $repository,
        ModelFactory $tokenModelFactory
    ) {
        $this->repository = $repository;
        $this->tokenModelFactory = $tokenModelFactory;
    }

    /**
     * Getting back the instance based on the customer ID
     *
     * @param string $customerId
     * @return ModelInterface
     */
    public function getTokenEntryByShopCustomerId(string $customerId): ModelInterface
    {
        if ($this->repository->existEntryByCustomerId($customerId)) {
            return $this->repository->getByCustomerId($customerId);
        }

        return $this->tokenModelFactory->create(['data' => ['customer_id' => $customerId]]);
    }
}
