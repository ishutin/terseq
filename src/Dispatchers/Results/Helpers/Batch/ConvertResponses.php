<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results\Helpers\Batch;

use Aws\DynamoDb\Marshaler;

trait ConvertResponses
{
    protected static function convertResponses(array $result, Marshaler $marshaler): array
    {
        if (isset($result['Responses'])) {
            foreach ($result['Responses'] as $table => $values) {
                foreach ($values as $key => $value) {
                    $result['Responses'][$table][$key] = $marshaler->unmarshalItem($value);
                }
            }
        }

        return $result;
    }
}
