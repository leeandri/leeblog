<?php

namespace App\Tests\E2E;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Panther\PantherTestCase;

class BasicTest extends PantherTestCase
{
    public function testEnvironmentIsOk(): void
    {
        $client = self::createPantherClient([
            'browser' => PantherTestCase::CHROME
        ]);
        $client->request(Request::METHOD_GET, '/');
        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'LeeBlog : ');
    }
}
