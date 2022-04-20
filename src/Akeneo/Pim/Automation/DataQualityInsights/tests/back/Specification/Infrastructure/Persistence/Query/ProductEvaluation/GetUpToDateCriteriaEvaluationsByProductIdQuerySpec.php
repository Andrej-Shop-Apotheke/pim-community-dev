<?php

declare(strict_types=1);

namespace Specification\Akeneo\Pim\Automation\DataQualityInsights\Infrastructure\Persistence\Query\ProductEvaluation;

use Akeneo\Pim\Automation\DataQualityInsights\Domain\Model\Read;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\Query\ProductEvaluation\GetCriteriaEvaluationsByProductIdQueryInterface;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\Query\ProductEvaluation\HasUpToDateEvaluationQueryInterface;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\CriterionCode;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\CriterionEvaluationStatus;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\ProductUuid;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;

/**
 * @copyright 2020 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
final class GetUpToDateCriteriaEvaluationsByProductIdQuerySpec extends ObjectBehavior
{
    public function let(
        GetCriteriaEvaluationsByProductIdQueryInterface $getCriteriaEvaluationsByProductIdQuery,
        HasUpToDateEvaluationQueryInterface $hasUpToDateEvaluationQuery
    ) {
        $this->beConstructedWith($getCriteriaEvaluationsByProductIdQuery, $hasUpToDateEvaluationQuery);
    }

    public function it_returns_criteria_evaluations_if_the_evaluation_of_the_product_is_up_to_date(
        GetCriteriaEvaluationsByProductIdQueryInterface $getCriteriaEvaluationsByProductIdQuery,
        HasUpToDateEvaluationQueryInterface $hasUpToDateEvaluationQuery
    ) {
        $productUuid = new ProductUuid(Uuid::fromString('df470d52-7723-4890-85a0-e79be625e2ed'));
        $hasUpToDateEvaluationQuery->forProductId($productUuid)->willReturn(true);

        $criteriaEvaluations = (new Read\CriterionEvaluationCollection())
            ->add(new Read\CriterionEvaluation(
                new CriterionCode('spelling'),
                new ProductUuid(Uuid::fromString('df470d52-7723-4890-85a0-e79be625e2ed')),
                new \DateTimeImmutable(),
                CriterionEvaluationStatus::pending(),
                null
        ));

        $getCriteriaEvaluationsByProductIdQuery->execute($productUuid)->willReturn($criteriaEvaluations);

        $this->execute($productUuid)->shouldReturn($criteriaEvaluations);
    }

    public function it_returns_empty_criteria_evaluations_if_the_evaluation_of_the_product_is_outdated(
        GetCriteriaEvaluationsByProductIdQueryInterface $getCriteriaEvaluationsByProductIdQuery,
        HasUpToDateEvaluationQueryInterface $hasUpToDateEvaluationQuery
    ) {
        $productUuid = new ProductUuid(Uuid::fromString('df470d52-7723-4890-85a0-e79be625e2ed'));
        $hasUpToDateEvaluationQuery->forProductId($productUuid)->willReturn(false);

        $getCriteriaEvaluationsByProductIdQuery->execute($productUuid)->shouldNotBeCalled();

        $this->execute($productUuid)->shouldBeLike(new Read\CriterionEvaluationCollection());
    }
}
