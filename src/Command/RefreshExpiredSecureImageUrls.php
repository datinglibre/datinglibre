<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ImageService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshExpiredSecureImageUrls extends Command
{
    protected static $defaultName = 'app:secure_urls:refresh_image_urls';
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    protected function configure()
    {
        $this ->setDescription('Refresh secure image urls');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->imageService->refreshSecureUrls();

        return 0;
    }
}
