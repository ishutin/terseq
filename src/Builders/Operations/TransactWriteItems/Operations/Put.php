<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\TransactWriteItems\Operations;

use Terseq\Builders\Builder;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\ConditionExpression;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\PutItem;
use Terseq\Builders\Shared\BuilderParts\ReturnValuesOnConditionCheckFailure;

class Put extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use ReturnValuesOnConditionCheckFailure;
    use PutItem;
    use ConditionExpression;

    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendReturnValuesOnConditionCheckFailure($config);
        $config = $this->appendItem($config);
        $config = $this->appendConditionExpression($config);

        return $this->appendAttributes($config);
    }
}
