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

namespace Akeneo\Category\Domain\ValueObject\Attribute;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeValuePerChannel
{
    private function __construct(
        private bool $value
    ) {
    }

    public static function fromBoolean(bool $hasOneValuePerChannel): self
    {
        return new self($hasOneValuePerChannel);
    }

    public function normalize(): bool
    {
        return $this->value;
    }

    public function isTrue(): bool
    {
        return $this->value;
    }
}
