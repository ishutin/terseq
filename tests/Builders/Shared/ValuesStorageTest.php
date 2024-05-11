<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Shared;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Shared\ValuesStorage;

#[CoversClass(ValuesStorage::class)]
final class ValuesStorageTest extends TestCase
{
    public function testGetValuesReturnsEmptyArrayWhenNoValuesAdded(): void
    {
        $valuesStorage = new ValuesStorage();
        $this->assertEquals([], $valuesStorage->getValues());
    }

    public function testCreateValueGeneratesUniqueNameForNewAttribute(): void
    {
        $valuesStorage = new ValuesStorage();
        $generatedName = $valuesStorage->createValue('attribute', 'value');
        $this->assertEquals(':attribute_0', $generatedName);
    }

    public function testCreateValueGeneratesUniqueNameForExistingAttribute(): void
    {
        $valuesStorage = new ValuesStorage();
        $valuesStorage->createValue('attribute', 'value1');
        $generatedName = $valuesStorage->createValue('attribute', 'value2');
        $this->assertEquals(':attribute_1', $generatedName);
    }

    public function testGetValuesReturnsAllValuesInCorrectOrder(): void
    {
        $valuesStorage = new ValuesStorage();
        $valuesStorage->createValue('attribute1', 'value1');
        $valuesStorage->createValue('attribute2', 'value2');
        $valuesStorage->createValue('attribute1', 'value3');
        $this->assertEquals([
            ':attribute1_0' => 'value1',
            ':attribute2_0' => 'value2',
            ':attribute1_1' => 'value3',
        ], $valuesStorage->getValues());
    }
}
