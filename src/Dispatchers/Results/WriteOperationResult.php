<?php

declare(strict_types=1);

namespace Terseq\Dispatchers\Results;

use Aws\DynamoDb\Marshaler;
use Terseq\Dispatchers\Results\Helpers\ConvertAttributes;
use Terseq\Dispatchers\Results\Helpers\ConvertItemCollectionMetrics;

/**
 * Response Elements
 *
 * If the action is successful, the service sends back an HTTP 200 response.
 *
 * The following data is returned in JSON format by the service.
 *
 * Attributes
 * A map of attribute values as they appear before or after the UpdateItem operation, as determined by the ReturnValues parameter.
 *
 * The Attributes map is only present if the update was successful and ReturnValues was specified as something other than NONE in the request. Each element represents one attribute.
 *
 * Type: String to AttributeValue object map
 *
 * Key Length Constraints: Maximum length of 65535.
 *
 * ConsumedCapacity
 * The capacity units consumed by the UpdateItem operation. The data returned includes the total provisioned throughput consumed, along with statistics for the table and any indexes involved in the operation. ConsumedCapacity is only returned if the ReturnConsumedCapacity parameter was specified. For more information, see Capacity unity consumption for write operations in the Amazon DynamoDB Developer Guide.
 *
 * Type: ConsumedCapacity object
 *
 * ItemCollectionMetrics
 * Information about item collections, if any, that were affected by the UpdateItem operation. ItemCollectionMetrics is only returned if the ReturnItemCollectionMetrics parameter was specified. If the table does not have any local secondary indexes, this information is not returned in the response.
 *
 * Each ItemCollectionMetrics element consists of:
 *
 * ItemCollectionKey - The partition key value of the item collection. This is the same as the partition key value of the item itself.
 *
 * SizeEstimateRangeGB - An estimate of item collection size, in gigabytes. This value is a two-element array containing a lower bound and an upper bound for the estimate. The estimate includes the size of all the items in the table, plus the size of all attributes projected into all of the local secondary indexes on that table. Use this estimate to measure whether a local secondary index is approaching its size limit.
 *
 * The estimate is subject to change over time; therefore, do not rely on the precision or accuracy of the estimate.
 *
 * Type: ItemCollectionMetrics object
 */
readonly class WriteOperationResult
{
    use ConvertItemCollectionMetrics;
    use ConvertAttributes;

    public function __construct(
        protected ?array $attributes,
        protected ?array $consumedCapacity,
        protected ?array $itemCollectionMetrics,
    ) {
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function getConsumedCapacity(): ?array
    {
        return $this->consumedCapacity;
    }

    public function getItemCollectionMetrics(): ?array
    {
        return $this->itemCollectionMetrics;
    }

    public static function create(
        array $result,
        Marshaler $marshaler = new Marshaler(),
    ): self {
        $result = static::convertAttributes($result, $marshaler);
        $result = static::convertItemCollectionMetric($result, $marshaler);

        return new self(
            attributes: $result['Attributes'] ?? null,
            consumedCapacity: $result['ConsumedCapacity'] ?? null,
            itemCollectionMetrics: $result['ItemCollectionMetrics'] ?? null,
        );
    }
}
