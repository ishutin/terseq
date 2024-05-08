<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\TransactGetItems;

use Closure;
use Terseq\Builders\Operations\Builder;
use Terseq\Builders\Operations\TransactGetItems\Operations\Get;
use Terseq\Builders\Shared\BuilderParts\ClientRequestToken;
use Terseq\Builders\Shared\BuilderParts\ReturnConsumedCapacity;
use Terseq\Builders\Shared\BuilderParts\ReturnItemCollectionMetrics;
use Terseq\Contracts\Builder\TableInterface;

use function array_map;

class TransactGetItems extends Builder
{
    use ReturnConsumedCapacity;
    use ReturnItemCollectionMetrics;
    use ClientRequestToken;

    /**
     * @var Get[]
     */
    protected array $get = [];

    public function get(Closure|array $closure, TableInterface|string|array|null $table = null): static
    {
        $clone = clone $this;
        if ($closure instanceof Closure) {
            $closure = [$closure];
        }

        foreach ($closure as $callback) {
            $clone->get[] = $callback(
                new Get(
                    table: $table ? $clone->createOrGetTable($table) : null,
                    marshaler: $clone->marshaler,
                ),
            );
        }

        return $clone;
    }

    public function getQuery(): array
    {
        $config = $this->createConfig(withoutTable: true);

        $config = $this->appendReturnConsumedCapacity($config);
        $config = $this->appendReturnItemCollectionMetrics($config);
        $config = $this->appendClientRequestToken($config);

        $config['TransactItems'] = array_map(static fn (Get $get) => ['Get' => $get->getQuery()], $this->get);

        return $config;
    }
}
