<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results\Helpers\Transact;

use Aws\DynamoDb\Marshaler;

trait ConvertResponses
{
    protected static function convertResponses(array $result, Marshaler $marshaler): array
    {
        if (isset($result['Responses'])) {
            foreach ($result['Responses'] as $key => $response) {
                $result['Responses'][$key] = $marshaler->unmarshalItem($response['Item']);
            }
        }

        return $result;
    }
}
