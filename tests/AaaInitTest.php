<?php
declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AaaInitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function initDb(): void
    {
        $this->assertTrue(true); // @phpstan-ignore method.alreadyNarrowedType
    }
}
