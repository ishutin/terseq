<?php

declare(strict_types=1);

namespace Terseq\Builders\Shared\BuilderParts;

trait SingleWriteOperations
{
    use ReturnConsumedCapacity;
    use ReturnItemCollectionMetrics;
    use ReturnValues;
    use ReturnValuesOnConditionCheckFailure;

    protected function appendWriteOperationData(array $config): array
    {
        $config = $this->appendReturnConsumedCapacity($config);
        $config = $this->appendReturnItemCollectionMetrics($config);
        $config = $this->appendReturnValues($config);
        return $this->appendReturnValuesOnConditionCheckFailure($config);
    }
}
