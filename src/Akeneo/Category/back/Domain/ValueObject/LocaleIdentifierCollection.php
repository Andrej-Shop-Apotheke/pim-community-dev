<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2018 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Category\Domain\ValueObject;

use Webmozart\Assert\Assert;

/**
 * @author    Laurent Petard <laurent.petard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class LocaleIdentifierCollection implements \IteratorAggregate
{
    /**
     * @param LocaleIdentifier[] $localeIdentifiers
     */
    public function __construct(
        private array $localeIdentifiers
    ) {
        Assert::allIsInstanceOf($localeIdentifiers, LocaleIdentifier::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->localeIdentifiers);
    }

    public function isEmpty(): bool
    {
        return empty($this->localeIdentifiers);
    }

    /**
     * @return string[]
     */
    public function normalize(): array
    {
        return array_map(static fn (LocaleIdentifier $localeIdentifier) => $localeIdentifier->normalize(), $this->localeIdentifiers);
    }

    /**
     * @param string[] $normalizedLocaleIdentifiers
     */
    public static function fromNormalized(array $normalizedLocaleIdentifiers): LocaleIdentifierCollection
    {
        $localeIdentifiers = array_map(static fn (string $localeIdentifier) => LocaleIdentifier::fromCode($localeIdentifier), $normalizedLocaleIdentifiers);

        return new self($localeIdentifiers);
    }

    public static function empty(): LocaleIdentifierCollection
    {
        return new self([]);
    }
}
