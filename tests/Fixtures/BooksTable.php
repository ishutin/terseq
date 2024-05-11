<?php

declare(strict_types=1);

namespace Terseq\Tests\Fixtures;

use Terseq\Builders\Keys;
use Terseq\Builders\Table;

class BooksTable extends Table
{
    public function getTableName(): string
    {
        return 'Books';
    }

    public function getKeys(): Keys
    {
        return new Keys(
            partitionKey: 'BookId',
            sortKey: 'ReleaseDate',
        );
    }

    public function getSecondaryIndexMap(): ?array
    {
        return [
            'Author' => new Keys(
                partitionKey: 'AuthorId',
                sortKey: 'BornDate',
            ),
        ];
    }
}
