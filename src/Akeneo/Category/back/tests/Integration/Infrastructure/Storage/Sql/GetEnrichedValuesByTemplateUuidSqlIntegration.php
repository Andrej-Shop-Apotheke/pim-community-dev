<?php

declare(strict_types=1);

namespace Akeneo\Category\back\tests\Integration\Infrastructure\Storage\Sql;

use Akeneo\Category\Application\Query\GetEnrichedValuesByTemplateUuid;
use Akeneo\Category\back\tests\Integration\Helper\CategoryTestCase;
use Akeneo\Category\Domain\ValueObject\Template\TemplateUuid;
use PHPUnit\Framework\Assert;

/**
 * @copyright 2023 Akeneo SAS (https://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GetEnrichedValuesByTemplateUuidSqlIntegration extends CategoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $socksCategory = $this->useTemplateFunctionalCatalog('6344aa2a-2be9-4093-b644-259ca7aee50c', 'socks');
        $winterSocksCategory = $this->createOrUpdateCategory(
            code: 'winter_socks',
            parentId: $socksCategory->getId()?->getValue(),
            rootId: $socksCategory->getId()?->getValue(),
        );
        $summerSocksCategory = $this->createOrUpdateCategory(
            code: 'summer_socks',
            parentId: $socksCategory->getId()?->getValue(),
            rootId: $socksCategory->getId()?->getValue(),
        );
        $japaneseSummerSocksCategory = $this->createOrUpdateCategory(
            code: 'japanese_summer_socks',
            parentId: $summerSocksCategory->getId()?->getValue(),
            rootId: $socksCategory->getId()?->getValue(),
        );
        $this->updateCategoryWithValues((string) $socksCategory->getCode());
        $this->updateCategoryWithValues((string) $winterSocksCategory->getCode());
        $this->updateCategoryWithValues((string) $summerSocksCategory->getCode());
        $this->updateCategoryWithValues((string) $japaneseSummerSocksCategory->getCode());

        $attributesUuids = $this->generateRandomUuidList(13);
        $pantsCategory = $this->useTemplateFunctionalCatalog('1294e06d-48a4-4055-abda-986d92bef8a2', 'pants', $attributesUuids);
        $this->updateCategoryWithValues((string) $pantsCategory->getCode());
    }

    public function testItRetrievesRelatedCategoriesByTemplateUuid(): void{
        $getEnrichedValuesByTemplateUuid = $this->get(GetEnrichedValuesByTemplateUuid::class);
        $result = ($getEnrichedValuesByTemplateUuid)(TemplateUuid::fromString('6344aa2a-2be9-4093-b644-259ca7aee50c'));

        Assert::assertArrayHasKey('socks', $result);
        Assert::assertArrayHasKey('winter_socks', $result);
        Assert::assertArrayHasKey('summer_socks', $result);
        Assert::assertNotEmpty($result['socks']->getValues());
        Assert::assertNotEmpty($result['winter_socks']->getValues());
        Assert::assertNotEmpty($result['winter_socks']->getValues());
        Assert::assertArrayNotHasKey('pants', $result);
    }
}
