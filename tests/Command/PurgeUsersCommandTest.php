<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\PurgeUsersCommand;
use App\Repository\UserRepository;
use App\Service\UserService;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class PurgeUsersCommandTest extends KernelTestCase
{
    const TEST_USER_EMAIL = 'test@example.com';
    const TEST_PASSWORD = 'password';
    const ONE_HOUR = 1;
    private ?UserRepository $userRepository;
    private ?UserService $userService;

    public function setUp(): void
    {
        self::bootKernel();
        $this->userRepository = self::$container->get('App\Repository\UserRepository');
        $this->userService = self::$container->get('App\Service\UserService');
        $this->userRepository->deleteByEmail(self::TEST_USER_EMAIL);
    }

    public function provider()
    {
        return [
            [PurgeUsersCommand::ALL, self::ONE_HOUR, true, new DateInterval('PT1H1M'), true],
            [PurgeUsersCommand::ALL, self::ONE_HOUR, true, new DateInterval('PT20M'), false],
            [PurgeUsersCommand::NOT_ENABLED, self::ONE_HOUR, true, new DateInterval('PT1H1M'), false],
            [PurgeUsersCommand::NOT_ENABLED, self::ONE_HOUR, false, new DateInterval('PT1H1M'), true],
            [PurgeUsersCommand::NOT_ENABLED, self::ONE_HOUR, false, new DateInterval('PT30M'), false],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testPurgeAllUsersCommandDeletesUserOlderThanAnHour(
        string $type,
        int $hours,
        bool $isEnabled,
        DateInterval $dateInterval,
        bool $expectedIsDeleted
    ) {
        $application = new Application(self::$kernel);
        $user = $this->userService->create(self::TEST_USER_EMAIL, self::TEST_PASSWORD, $isEnabled, []);

        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->userRepository->updateCreatedAt($user->getId(), $now->sub($dateInterval));

        $command = $application->find('app:users:purge');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'type' => $type,
            'hours' => $hours
        ]);

        $isDeleted = $this->userService->findByEmail(self::TEST_USER_EMAIL) == null;
        $this->assertEquals($isDeleted, $expectedIsDeleted);
    }
}
