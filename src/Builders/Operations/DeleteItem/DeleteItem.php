<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\DeleteItem;

use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\HasOperationByKey;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;

class DeleteItem extends Builder
{
    use HasOperationByKey;
    use SingleWriteOperations;


    // https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_DeleteItem.html
    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendWriteOperationData($config);

        return $this->appendKey($config);
    }
}
