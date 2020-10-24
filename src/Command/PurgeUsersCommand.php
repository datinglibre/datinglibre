<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeUsersCommand extends Command
{
    protected static $defaultName = 'app:users:purge';
    public const ALL = 'ALL';
    public const NOT_ENABLED = 'NOT_ENABLED';
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    protected function configure()
    {
        $this ->setDescription('Deletes users periodically');

        $this->addArgument(
            'type',
            InputArgument::REQUIRED,
            'Type of user you want to purge (ALL, NOT_ENABLED)'
        );

        $this->addArgument(
            'hours',
            InputArgument::REQUIRED,
            'Age of the user in hours'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->userService->purge($input->getArgument('type'), (int) $input->getArgument('hours'));

        return 0;
    }
}
