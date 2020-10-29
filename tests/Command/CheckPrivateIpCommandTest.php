<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CheckPrivateIpCommandTest extends KernelTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

    public function provider()
    {
        return [
            ['192.168.0.1', 0],
            ['64.233.160.0', 1],
            ['10.106.0.2', 0],
            ['invalid', 1]
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testCommandDetectsPrivateAndPublicIps(string $ip, int $expectedReturnValue)
    {
        $application = new Application(self::$kernel);
        $command = $application->find('app:ip:is_private');
        $commandTester = new CommandTester($command);

        $this->assertEquals($commandTester->execute(['ip' => $ip]), $expectedReturnValue);
    }
}
