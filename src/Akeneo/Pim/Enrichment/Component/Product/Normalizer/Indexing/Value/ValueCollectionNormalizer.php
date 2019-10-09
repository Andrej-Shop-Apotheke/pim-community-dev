<?php

namespace Akeneo\Pim\Enrichment\Component\Product\Normalizer\Indexing\Value;

use Akeneo\Pim\Enrichment\Component\Product\Model\WriteValueCollection;
use Akeneo\Pim\Enrichment\Component\Product\Normalizer\Indexing\ProductAndProductModel;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer for product value collection
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @author    Anaël Chardan <anael.chardan@akeneo.com>
 * @author    Philippe Mossière <philippe.mossiere@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ValueCollectionNormalizer implements NormalizerInterface
{
    public const INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX = 'indexing_product_and_product_model';

    /** @var NormalizerInterface */
    private $serializer;

    public function __construct(NormalizerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($values, $format = null, array $context = [])
    {
        $normalizedValues = [];
        foreach ($values as $value) {
            $normalizedValues[] = $this->serializer->normalize($value, $format, $context);
        }

        $result = empty($normalizedValues) ? [] : array_replace_recursive(...$normalizedValues);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof WriteValueCollection && (
                $format === self::INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX
            );
    }
}
