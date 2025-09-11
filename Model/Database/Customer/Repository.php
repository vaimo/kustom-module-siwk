<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Database\Customer;

use Klarna\Base\Model\RepositoryAbstract;
use Klarna\Siwk\Api\Database\Customer\ModelInterface;
use Klarna\Siwk\Api\Database\Customer\RepositoryInterface;
use Klarna\Siwk\Model\Database\Customer\ResourceModel\Model as ResourceModel;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @internal
 */
class Repository extends RepositoryAbstract implements RepositoryInterface
{

    /**
     * @param ModelFactory $modelFactory
     * @param ResourceModel $resourceModel
     * @codeCoverageIgnore
     */
    public function __construct(ModelFactory $modelFactory, ResourceModel $resourceModel)
    {
        parent::__construct($resourceModel, $modelFactory);
    }

    /**
     * @inheritDoc
     */
    public function existEntryByKlarnaCustomerId(string $customerId): bool
    {
        return $this->existEntryByKeyValuePair('klarna_customer_id', $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getByKlarnaCustomerId(string $customerId): ModelInterface
    {
        return $this->getByKeyValuePair('klarna_customer_id', $customerId);
    }
}
