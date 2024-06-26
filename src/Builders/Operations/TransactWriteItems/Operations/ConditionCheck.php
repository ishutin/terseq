<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\TransactWriteItems\Operations;

use Terseq\Builders\Builder;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\ConditionExpression;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\HasOperationByKey;
use Terseq\Builders\Shared\BuilderParts\ReturnValuesOnConditionCheckFailure;

class ConditionCheck extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use HasOperationByKey;
    use ReturnValuesOnConditionCheckFailure;
    use ConditionExpression;

    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendKey($config);
        $config = $this->appendReturnValuesOnConditionCheckFailure($config);
        $config = $this->appendConditionExpression($config);

        return $this->appendAttributes($config);
    }
}
