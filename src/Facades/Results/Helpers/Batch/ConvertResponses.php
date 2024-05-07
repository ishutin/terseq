<?php

declare(strict_types=1);

namespace Terseq\Facades\Results\Helpers\Batch;

use Aws\DynamoDb\Marshaler;

trait ConvertResponses
{
    protected static function convertResponses(array $result, Marshaler $marshaler): array
    {
        if (isset($result['Responses'])) {
            foreach ($result['Responses'] as $key => $value) {
                $result['Responses'][$key] = array_map(
                    static fn (array $attribute) => $marshaler->unmarshalItem($attribute),
                    $result['Responses'][$key],
                );
            }
        }

        return $result;
    }
}
