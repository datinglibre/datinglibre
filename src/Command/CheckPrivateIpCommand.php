<?php

declare(strict_types=1);

namespace App\Command;

use IPLib\Factory;
use IPLib\Range\Type;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckPrivateIpCommand extends Command
{
    protected static $defaultName = 'app:ip:is_private';

    protected function configure()
    {
        $this ->setDescription('Checks whether an IP address is in a private subnet');

        $this->addArgument(
            'ip',
            InputArgument::REQUIRED,
            'IP address'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $address = Factory::addressFromString($input->getArgument('ip'));

        if ($address == null) {
            return 1;
        }

        return $address->getRangeType() === Type::T_PRIVATENETWORK ? 0 : 1;
    }
}
