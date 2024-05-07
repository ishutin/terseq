<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\TransactWriteItems\Operations;

use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\HasOperationByKey;
use Terseq\Builders\Shared\BuilderParts\ReturnValuesOnConditionCheckFailure;
use Terseq\Builders\Shared\BuilderParts\UpdateExpression;

class Update extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use ReturnValuesOnConditionCheckFailure;
    use UpdateExpression;
    use HasOperationByKey;

    // todo: ConditionExpression

    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendReturnValuesOnConditionCheckFailure($config);
        $config = $this->appendUpdateExpression($config);
        $config = $this->appendAttributes($config);
        $config = $this->appendKey($config);

        return $this->appendAttributes($config);
    }
}
