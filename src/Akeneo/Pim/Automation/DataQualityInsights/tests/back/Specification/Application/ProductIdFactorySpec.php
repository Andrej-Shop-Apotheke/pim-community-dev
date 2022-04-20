<?php

namespace Specification\Akeneo\Pim\Automation\DataQualityInsights\Application;

use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\ProductUuid;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class ProductIdFactorySpec extends ObjectBehavior
{
    public function it_creates_a_product_id()
    {
        $this->create(Uuid::fromString('df470d52-7723-4890-85a0-e79be625e2ed'))->shouldBeLike(new ProductUuid(Uuid::fromString('df470d52-7723-4890-85a0-e79be625e2ed')));
    }

    public function it_creates_a_collection_of_product_id()
    {
        $collectionBehavior = $this->createCollection(['1234', '4321']);

        $collection = $collectionBehavior->getWrappedObject();
        Assert::allIsInstanceOf($collection, ProductUuid:: class);
        Assert::same((string) $collection->toArray()[0], '1234');
        Assert::same((string) $collection->toArray()[1], '4321');
    }

    public function it_throws_exception_when_invalid_id()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('create', ['abcd']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('create', ['-1234']);
    }

    public function it_throws_exception_when_invalid_list_of_ids()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createCollection', [['abcd']]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('createCollection', [['-1234']]);
    }
}
