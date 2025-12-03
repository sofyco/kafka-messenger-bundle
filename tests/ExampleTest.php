<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\Tests;

use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        restore_exception_handler();
    }

    public function testExample(): void
    {
        self::assertSameSize([1], [2]);
    }
}
