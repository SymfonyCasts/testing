<?php

namespace App\Tests\Integration\Service;

use App\Service\GithubService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class AServiceTest extends KernelTestCase
{
    public function testTest()
    {
        self::bootKernel();

        self::assertInstanceOf(GithubService::class, static::getContainer()->get(GithubService::class));
    }
}
