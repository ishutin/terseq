<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\HasOperationByKey;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Builders\Shared\BuilderParts\ConditionExpression;

class DeleteItem extends Builder
{
    use HasOperationByKey;
    use SingleWriteOperations;
    use ConditionExpression;
    use HasAttributes;
    use AppendAttributes;

    // https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_DeleteItem.html
    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendWriteOperationData($config);
        $config = $this->appendAttributes($config);
        $config = $this->appendConditionExpression($config);

        return $this->appendKey($config);
    }
}
