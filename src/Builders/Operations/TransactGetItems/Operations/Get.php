<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\TransactGetItems\Operations;

use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\HasOperationByKey;
use Terseq\Builders\Shared\BuilderParts\ProjectionExpression;

class Get extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use HasOperationByKey;
    use ProjectionExpression;

    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendProjectionExpression($config);
        $config = $this->appendAttributes($config, withValues: false);

        return $this->appendKey($config);
    }
}
