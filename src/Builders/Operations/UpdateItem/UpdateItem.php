<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\UpdateItem;

use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\HasOperationByKey;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;
use Terseq\Builders\Shared\BuilderParts\UpdateExpression;

class UpdateItem extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use HasOperationByKey;
    use SingleWriteOperations;
    use UpdateExpression;

    // https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_UpdateItem.html
    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendWriteOperationData($config);
        $config = $this->appendUpdateExpression($config);
        $config = $this->appendAttributes($config);

        return $this->appendKey($config);
    }


}
