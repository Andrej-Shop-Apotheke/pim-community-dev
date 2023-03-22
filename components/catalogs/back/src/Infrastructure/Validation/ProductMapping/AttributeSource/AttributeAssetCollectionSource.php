<?php

declare(strict_types=1);

namespace Akeneo\Catalogs\Infrastructure\Validation\ProductMapping\AttributeSource;

use Akeneo\Catalogs\Infrastructure\Validation\ProductMapping\AssetAttributeSourceContainsValidLocale;
use Akeneo\Catalogs\Infrastructure\Validation\ProductMapping\AssetAttributeSourceContainsValidScope;
use Akeneo\Catalogs\Infrastructure\Validation\ProductMapping\AttributeSourceContainsValidLocale;
use Akeneo\Catalogs\Infrastructure\Validation\ProductMapping\AttributeSourceContainsValidScope;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

/**
 * @copyright 2023 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AttributeAssetCollectionSource extends Compound
{
    /**
     * @param array<array-key, mixed> $options
     *
     * @return array<array-key, Constraint>
     */
    protected function getConstraints(array $options = []): array
    {
        return [
            new Assert\Sequentially([
                new Assert\Collection([
                    'fields' => [
                        'source' => [
                            new Assert\Type('string'),
                            new Assert\NotBlank(),
                        ],
                        'scope' => [
                            new Assert\Type('string'),
                            new Assert\NotBlank(allowNull: true),
                        ],
                        'locale' => [
                            new Assert\Type('string'),
                            new Assert\NotBlank(allowNull: true),
                        ],
                        'parameters' => [
                            new Assert\Collection([
                                'fields' => [
                                    'sub_source' => [
                                        new Assert\Type('string'),
                                        new Assert\NotBlank(),
                                    ],
                                    'sub_scope' => [
                                        new Assert\Type('string'),
                                        new Assert\NotBlank(allowNull: true),
                                    ],
                                    'sub_locale' => [
                                        new Assert\Type('string'),
                                        new Assert\NotBlank(allowNull: true),
                                    ],
                                ],
                                'allowMissingFields' => false,
                                'allowExtraFields' => false,
                            ]),
                            new AssetAttributeSourceContainsValidScope(),
                            new AssetAttributeSourceContainsValidLocale(),
                        ],
                    ],
                    'allowMissingFields' => false,
                    'allowExtraFields' => false,
                ]),
                new AttributeSourceContainsValidScope(),
                new AttributeSourceContainsValidLocale(),
            ]),
        ];
    }
}