<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\GetItem;

use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\ConsistentRead;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\HasOperationByKey;
use Terseq\Builders\Shared\BuilderParts\ProjectionExpression;
use Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity;
use Terseq\Builders\Shared\Extends\HasCasters;

class GetItem extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use HasOperationByKey;
    use ProjectionExpression;
    use ConsistentRead;
    use ReturnConsumedCapacity;

    use HasCasters;

    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendProjectionExpression($config);
        $config = $this->appendAttributes($config);
        $config = $this->appendConsistentRead($config);
        $config = $this->appendReturnConsumedCapacity($config);

        return $this->appendKey($config);
    }
}
