<?php

declare(strict_types=1);

namespace Specification\Akeneo\Category\Application\Applier;

use Akeneo\Category\Api\Command\UserIntents\SetRichText;
use Akeneo\Category\Application\Applier\SetRichTextApplier;
use Akeneo\Category\Domain\Model\Category;
use Akeneo\Category\Domain\ValueObject\CategoryId;
use Akeneo\Category\Domain\ValueObject\Code;
use Akeneo\Category\Domain\ValueObject\LabelCollection;
use Akeneo\Category\Domain\ValueObject\ValueCollection;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\Assert;

/**
 * @copyright 2022 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class SetRichTextApplierSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SetRichTextApplier::class);
    }

    function it_updates_category_value_collection()
    {
        $valueKey = 'attribute_code'
            . ValueCollection::SEPARATOR . 'uuid' .
            ValueCollection::SEPARATOR . 'locale_code';

        $valueCollection = ValueCollection::fromArray(
            [
                $valueKey => [
                    'data' => 'value',
                    'locale' => 'locale_code',
                    'attribute_code' => 'attribute_code' . ValueCollection::SEPARATOR . 'uuid'
                ]
            ]
        );

        $category = new Category(
            id: new CategoryId(1),
            code: new Code('code'),
            labelCollection: LabelCollection::fromArray([]),
            valueCollection: $valueCollection
        );

        $userIntent = new SetRichText(
            'uuid',
            'attribute_code',
            'locale_code',
            'updated_value'
        );

        $expectedValueCollection = ValueCollection::fromArray(
            [
                $valueKey => [
                    'data' => 'updated_value',
                    'locale' => 'locale_code',
                    'attribute_code' => 'attribute_code' . ValueCollection::SEPARATOR . 'uuid'
                ]
            ]
        );

        $expectedCategory = new Category(
            id: new CategoryId(1),
            code: new Code('code'),
            labelCollection: LabelCollection::fromArray([]),
            valueCollection: $expectedValueCollection
        );

        $this->apply($userIntent, $category);
        Assert::assertEquals(
            $expectedCategory->getValueCollection(),
            $category->getValueCollection()
        );
    }
}