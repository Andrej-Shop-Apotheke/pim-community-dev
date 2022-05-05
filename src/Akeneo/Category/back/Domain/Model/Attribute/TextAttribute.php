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

namespace Akeneo\Category\Domain\Model\Attribute;

use Akeneo\Category\Domain\ValueObject\Attribute\AttributeCode;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeIdentifier;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeIsRequired;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeIsRichTextEditor;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeIsTextarea;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeMaxLength;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeOrder;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeRegularExpression;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeValidationRule;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeValuePerChannel;
use Akeneo\Category\Domain\ValueObject\Attribute\AttributeValuePerLocale;
use Akeneo\Category\Domain\ValueObject\LabelCollection;
use Akeneo\Category\Domain\ValueObject\TemplateIdentifier;
use Webmozart\Assert\Assert;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextAttribute extends AbstractAttribute
{
    public const ATTRIBUTE_TYPE = 'text';

    protected function __construct(
        AttributeIdentifier                $identifier,
        TemplateIdentifier                 $templateIdentifier,
        AttributeCode                      $code,
        LabelCollection                    $labelCollection,
        AttributeOrder                     $order,
        AttributeIsRequired                $isRequired,
        AttributeValuePerChannel           $valuePerChannel,
        AttributeValuePerLocale            $valuePerLocale,
        private AttributeMaxLength         $maxLength,
        private AttributeIsTextarea        $isTextarea,
        private AttributeValidationRule    $validationRule,
        private AttributeRegularExpression $regularExpression,
        private AttributeIsRichTextEditor $isRichTextEditor
    ) {
        if ($isTextarea->isYes()) {
            Assert::true(
                $validationRule->isNone() && $regularExpression->isEmpty(),
                'It is not possible to create a text area attribute with a validation rule.'
            );
        } else {
            Assert::false($isRichTextEditor->isYes());
            if ($validationRule->isRegularExpression()) {
                Assert::false(
                    $regularExpression->isEmpty(),
                    'It is not possible to create a text attribute with a regular expression without specifying it'
                );
            }
        }
        parent::__construct(
            $identifier,
            $templateIdentifier,
            $code,
            $labelCollection,
            $order,
            $isRequired,
            $valuePerChannel,
            $valuePerLocale
        );
    }

    public static function createText(
        AttributeIdentifier $identifier,
        TemplateIdentifier $templateIdentifier,
        AttributeCode $code,
        LabelCollection $labelCollection,
        AttributeOrder $order,
        AttributeIsRequired $isRequired,
        AttributeValuePerChannel $valuePerChannel,
        AttributeValuePerLocale $valuePerLocale,
        AttributeMaxLength $maxLength,
        AttributeValidationRule $validationRule,
        AttributeRegularExpression $regularExpression
    ) {
        return new self(
            $identifier,
            $templateIdentifier,
            $code,
            $labelCollection,
            $order,
            $isRequired,
            $valuePerChannel,
            $valuePerLocale,
            $maxLength,
            AttributeIsTextarea::fromBoolean(false),
            $validationRule,
            $regularExpression,
            AttributeIsRichTextEditor::fromBoolean(false)
        );
    }

    public static function createTextarea(
        AttributeIdentifier $identifier,
        TemplateIdentifier $templateIdentifier,
        AttributeCode $code,
        LabelCollection $labelCollection,
        AttributeOrder $order,
        AttributeIsRequired $isRequired,
        AttributeValuePerChannel $valuePerChannel,
        AttributeValuePerLocale $valuePerLocale,
        AttributeMaxLength $maxLength,
        AttributeIsRichTextEditor $isRichTextEditor
    ) {
        return new self(
            $identifier,
            $templateIdentifier,
            $code,
            $labelCollection,
            $order,
            $isRequired,
            $valuePerChannel,
            $valuePerLocale,
            $maxLength,
            AttributeIsTextarea::fromBoolean(true),
            AttributeValidationRule::none(),
            AttributeRegularExpression::createEmpty(),
            $isRichTextEditor
        );
    }

    public function normalize(): array
    {
        return array_merge(
            parent::normalize(),
            [
                'max_length' => $this->maxLength->normalize(),
                'is_textarea' => $this->isTextarea->normalize(),
                'is_rich_text_editor' => $this->isRichTextEditor->normalize(),
                'validation_rule' => $this->validationRule->normalize(),
                'regular_expression' => $this->regularExpression->normalize(),
            ]
        );
    }

    public function setIsTextarea(AttributeIsTextarea $isTextarea): void
    {
        if ($this->isTextarea->equals($isTextarea)) {
            return;
        }
        $this->isTextarea = $isTextarea;
        $this->isRichTextEditor = AttributeIsRichTextEditor::fromBoolean(false);
        $this->validationRule = AttributeValidationRule::none();
        $this->regularExpression = AttributeRegularExpression::createEmpty();
    }

    public function setValidationRule(AttributeValidationRule $validationRule): void
    {
        if ($this->isTextarea->isYes() && !$validationRule->isNone()) {
            throw new \LogicException('Cannot update the validation rule when the text area flag is true');
        }
        $this->validationRule = $validationRule;
        if (!$this->validationRule->isRegularExpression()) {
            $this->regularExpression = AttributeRegularExpression::createEmpty();
        }
    }

    public function setRegularExpression(AttributeRegularExpression $regularExpression): void
    {
        if (!$regularExpression->isEmpty()) {
            if ($this->isTextarea->isYes()) {
                throw new \LogicException('Cannot update the regular expression when the text area flag is true');
            }
            if (!$this->validationRule->isRegularExpression()) {
                throw new \LogicException('Cannot update the regular expression when the validation rule is not set to regular expression');
            }
        }
        $this->regularExpression = $regularExpression;
    }

    public function getRegularExpression(): AttributeRegularExpression
    {
        return $this->regularExpression;
    }

    public function setIsRichTextEditor(AttributeIsRichTextEditor $isRichTextEditor): void
    {
        if (!$this->isTextarea->isYes() && $isRichTextEditor->isYes()) {
            throw new \LogicException('Cannot update the is rich text editor flag when the text area flag is false');
        }
        $this->isRichTextEditor = $isRichTextEditor;
    }

    public function setMaxLength(AttributeMaxLength $newMaxLength): void
    {
        $this->maxLength = $newMaxLength;
    }

    public function getMaxLength(): AttributeMaxLength
    {
        return $this->maxLength;
    }

    public function isTextarea(): bool
    {
        return $this->isTextarea->isYes();
    }

    public function isValidationRuleSetToRegularExpression(): bool
    {
        return $this->validationRule->isRegularExpression();
    }

    public function isValidationRuleSetToEmail(): bool
    {
        return $this->validationRule->isEmail();
    }

    public function isValidationRuleSetToUrl(): bool
    {
        return $this->validationRule->isUrl();
    }

    public function hasValidationRule(): bool
    {
        return $this->validationRule->isNone();
    }

    public function getType(): string
    {
        return self::ATTRIBUTE_TYPE;
    }
}
