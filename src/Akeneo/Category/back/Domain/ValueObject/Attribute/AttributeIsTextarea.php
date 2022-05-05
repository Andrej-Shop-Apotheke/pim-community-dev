<?php

declare(strict_types=1);

namespace Akeneo\Category\Domain\ValueObject\Attribute;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class AttributeIsTextarea
{
    private function __construct(
        private bool $isTextarea
    ) {
    }

    public static function fromBoolean(bool $isTextarea): self
    {
        return new self($isTextarea);
    }

    public function isYes(): bool
    {
        return $this->isTextarea;
    }

    public function normalize(): bool
    {
        return $this->isTextarea;
    }

    public function equals(AttributeIsTextarea $otherIsTextarea): bool
    {
        return $this->isTextarea === $otherIsTextarea->isTextarea;
    }
}
