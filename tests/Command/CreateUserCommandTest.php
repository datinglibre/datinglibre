<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends KernelTestCase
{
    const TEST_USER_EMAIL = 'test@example.com';
    private ?UserRepository $userRepository;

    public function setUp(): void
    {
        self::bootKernel();
        $this->userRepository = self::$container->get('App\Repository\UserRepository');
        $this->userRepository->deleteByEmail(self::TEST_USER_EMAIL);
    }

    public function testCreateUserCommandCreatesUser()
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:users:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'test@example.com',
            'role' => 'user',
            'password' => 'password'
        ]);

        $this->assertNotNull($this->userRepository->findOneBy([User::EMAIL => self::TEST_USER_EMAIL]));
    }
}
