<?php

declare(strict_types=1);

namespace Terseq\Builders;

use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\PutItem as PutItemTrait;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Builders\Shared\BuilderParts\ConditionExpression;

class PutItem extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use SingleWriteOperations;
    use ConditionExpression;
    use PutItemTrait;

    // https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_PutItem.html
    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendWriteOperationData($config);
        $config = $this->appendAttributes($config);
        $config = $this->appendConditionExpression($config);

        return $this->appendItem($config);
    }
}
