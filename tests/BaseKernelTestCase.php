<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseKernelTestCase extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
