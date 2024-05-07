<?php

declare(strict_types=1);

namespace Terseq\Facades\Results\Helpers\Transact;

use Aws\DynamoDb\Marshaler;

trait ConvertMultiplyItemCollectionMetrics
{
    protected static function convertMultiplyItemCollectionMetric(array $result, Marshaler $marshaler): array
    {
        if (isset($result['ItemCollectionMetrics'])) {
            foreach ($result['ItemCollectionMetrics'] as $key => $itemCollection) {
                foreach ($itemCollection as $keyItemCollection => $itemCollectionKey) {
                    $result['ItemCollectionMetrics'][$key][$keyItemCollection]['ItemCollectionKey'] = $marshaler
                        ->unmarshalItem($itemCollectionKey['ItemCollectionKey']);
                }
            }
        }

        return $result;
    }
}
