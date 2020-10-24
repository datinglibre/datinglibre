<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Service\UserService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:users:create';
    const MODERATOR = 'MODERATOR';
    const ADMIN = 'ADMIN';
    const USER = 'USER';
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    protected function configure()
    {
        $this ->setDescription('Creates a new USER, MODERATOR or ADMIN.');

        $this->addArgument(
            'email',
            InputArgument::REQUIRED,
            'Email'
        );

        $this->addArgument(
            'password',
            InputArgument::REQUIRED,
            'Password in plain text'
        );

        $this->addArgument(
            'role',
            InputArgument::REQUIRED,
            'USER, MODERATOR or ADMIN'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');

        if (null !== $this->userService->findByEmail($email)) {
            $output->writeln(sprintf('User with email %s already exists.', $email));
            return 1;
        }

        $this->userService->create(
            $email,
            $input->getArgument('password'),
            true,
            $this->parseRole($input->getArgument('role'))
        );

        return 0;
    }

    private function parseRole(string $role): array
    {
        if (strtoupper($role) === self::MODERATOR) {
            return [User::MODERATOR];
        }

        if (strtoupper($role) === self::ADMIN) {
            return [User::ADMIN];
        }

        if (strtoupper($role) === self::USER) {
            // User role always added automatically
            return [];
        }

        throw new Exception(sprintf(
            'Role not recognised %s, roles are %s, %s and %s',
            $role,
            self::MODERATOR,
            self::ADMIN,
            self::USER
        ));
    }
}
