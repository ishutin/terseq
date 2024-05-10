<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results\Helpers;

use Aws\DynamoDb\Marshaler;

trait ConvertAttributes
{
    protected static function convertAttributes(array $result, Marshaler $marshaler): array
    {
        if (isset($result['Attributes'])) {
            foreach ($result['Attributes'] as $key => $attribute) {
                $result['Attributes'][$key] = $marshaler->unmarshalItem($attribute);
            }
        }

        return $result;
    }
}
