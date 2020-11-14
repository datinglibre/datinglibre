<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RefreshExpiredSecureImageUrlsTest extends KernelTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

    // if this test fails, you also need to update the Ansible
    // task that creates the cron job with the new command name
    public function testCanFindRefreshExpiredSecureImageUrlsCommand()
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:secure_urls:refresh_image_urls');
        $this->assertNotNull($command);
    }
}
