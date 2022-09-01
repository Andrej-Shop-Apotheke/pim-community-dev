<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Product\Test\Acceptance\InMemory;

use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductRepositoryInterface;
use Akeneo\Pim\Enrichment\Product\Domain\Query\GetProductUuids;
use Ramsey\Uuid\UuidInterface;

/**
 * @copyright 2022 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InMemoryGetProductUuids implements GetProductUuids
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    public function fromIdentifier(string $identifier): ?UuidInterface
    {
        return $this->productRepository->findOneByIdentifier($identifier)?->getUuid();
    }

    /**
     * {@inheritdoc}
     */
    public function fromIdentifiers(array $identifiers): array
    {
        $result = [];
        foreach ($identifiers as $identifier) {
            $product = $this->productRepository->findOneByIdentifier($identifier);
            if (null !== $product) {
                $result[$identifier] = $product->getUuid();
            }
        }

        return $result;
    }
}
