<?php

declare(strict_types=1);

namespace Terseq\Tests\Builders\Shared\BuilderParts;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Terseq\Builders\Shared\BuilderParts\ClientRequestToken;

#[CoversClass(ClientRequestToken::class)]
class ClientRequestTokenTest extends TestCase
{
    public function testClientRequestTokenSetsTokenAndReturnsClone(): void
    {
        $object = new class () {
            use ClientRequestToken;
        };

        $clone = $object->clientRequestToken('token');
        $this->assertNotSame($object, $clone);
    }

    public function testAppendClientRequestTokenAddsTokenToConfigWhenSet(): void
    {
        $object = new class () {
            use ClientRequestToken;

            public function append(array $config)
            {
                return $this->appendClientRequestToken($config);
            }
        };

        $object = $object->clientRequestToken('token');
        $config = $object->append([]);
        $this->assertEquals(['ClientRequestToken' => 'token'], $config);
    }

    public function testAppendClientRequestTokenDoesNotAddTokenToConfigWhenNotSet(): void
    {
        $object = new class () {
            use ClientRequestToken;

            public function append(array $config)
            {
                return $this->appendClientRequestToken($config);
            }
        };

        $config = $object->append([]);
        $this->assertEquals([], $config);
    }
}
