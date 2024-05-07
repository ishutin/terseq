<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\PutItem;

use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Shared\BuilderParts\AppendAttributes;
use Terseq\Builders\Shared\BuilderParts\HasAttributes;
use Terseq\Builders\Shared\BuilderParts\PutItem as PutItemTrait;
use Terseq\Builders\Shared\BuilderParts\SingleWriteOperations;

class PutItem extends Builder
{
    use HasAttributes;
    use AppendAttributes;
    use SingleWriteOperations;
    use PutItemTrait;

    // https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_PutItem.html
    public function getQuery(): array
    {
        $config = $this->createConfig();

        $config = $this->appendWriteOperationData($config);
        return $this->appendItem($config);
    }
}
