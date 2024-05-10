<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results\Helpers;

use Aws\DynamoDb\Marshaler;

trait ConvertItemCollectionMetrics
{
    protected static function convertItemCollectionMetric(array $result, Marshaler $marshaler): array
    {
        if (isset($result['ItemCollectionMetrics'])) {
            foreach ($result['ItemCollectionMetrics']['ItemCollectionKey'] as $key => $itemCollection) {
                $result['ItemCollectionMetrics']['ItemCollectionKey'][$key] = $marshaler
                    ->unmarshalItem($itemCollection);
            }
        }

        return $result;
    }
}
