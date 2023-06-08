<?php

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BasicTest extends KernelTestCase
{
    public function testEnvironmentIsOK(): void
    {
        $this->assertTrue(true);
    }
}
