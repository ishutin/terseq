<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results\Helpers;

use Aws\DynamoDb\Marshaler;

trait ConvertAttributes
{
    protected static function convertAttributes(array $result, Marshaler $marshaler): array
    {
        if (isset($result['Attributes'])) {
            $result['Attributes'] = $marshaler->unmarshalItem($result['Attributes']);
        }

        return $result;
    }
}
