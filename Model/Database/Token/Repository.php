<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Database\Token;

use Klarna\Base\Model\RepositoryAbstract;
use Klarna\Siwk\Api\Database\Token\RepositoryInterface;
use Klarna\Siwk\Api\Database\Token\ModelInterface;
use Klarna\Siwk\Model\Database\Token\ModelFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use \Klarna\Siwk\Model\Database\Token\ResourceModel\Siwk;

/**
 * @internal
 */
class Repository extends RepositoryAbstract implements RepositoryInterface
{
    /**
     * @param ModelFactory $tokenModelFactory
     * @param Siwk $resourceModel
     * @codeCoverageIgnore
     */
    public function __construct(ModelFactory $tokenModelFactory, Siwk $resourceModel)
    {
        parent::__construct($resourceModel, $tokenModelFactory);
    }

    /**
     * @inheritDoc
     */
    public function getByCustomerId(string $customerId): ModelInterface
    {
        return $this->getByKeyValuePair('customer_id', $customerId);
    }

    /**
     * @inheritDoc
     */
    public function existEntryByCustomerId(string $customerId): bool
    {
        return $this->existEntryByKeyValuePair('customer_id', $customerId);
    }
}
