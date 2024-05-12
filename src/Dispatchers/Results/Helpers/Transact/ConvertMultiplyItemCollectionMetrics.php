<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results\Helpers\Transact;

use Aws\DynamoDb\Marshaler;

trait ConvertMultiplyItemCollectionMetrics
{
    protected static function convertMultiplyItemCollectionMetric(array $result, Marshaler $marshaler): array
    {
        if (isset($result['ItemCollectionMetrics'])) {
            foreach ($result['ItemCollectionMetrics'] as $table => $itemCollection) {
                foreach ($result['ItemCollectionMetrics'][$table] as $key => $item) {
                    $result['ItemCollectionMetrics'][$table][$key]['ItemCollectionKey'] = $marshaler->unmarshalItem(
                        $item['ItemCollectionKey'],
                    );
                }
            }
        }

        return $result;
    }
}
