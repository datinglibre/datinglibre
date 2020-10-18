<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\Block;
use App\Entity\BlockReason;
use App\Repository\BlockReasonRepository;
use App\Repository\BlockRepository;
use App\Service\UserService;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Webmozart\Assert\Assert;

class BlockContext implements Context
{
    private BlockRepository $blockRepository;
    private BlockReasonRepository $blockReasonRepository;
    private UserService $userService;

    public function __construct(
        UserService $userService,
        BlockRepository $blockRepository,
        BlockReasonRepository $blockReasonRepository
    ) {
        $this->userService = $userService;
        $this->blockRepository = $blockRepository;
        $this->blockReasonRepository = $blockReasonRepository;
    }

    /**
     * @Given the following blocks exist
     */
    public function theFollowingBlocksExist(TableNode $table)
    {
        foreach ($table as $row) {
            $user = $this->userService->findByEmail($row['email']);
            $blockedUser = $this->userService->findByEmail($row['block']);
            Assert::notNull($blockedUser);
            $block = new Block();
            $block->setUser($user);
            $block->setBlockedUser($blockedUser);
            $blockReason = $this->blockReasonRepository->findOneBy([BlockReason::NAME => 'block.spam']);
            $block->setReason($blockReason);

            $this->blockRepository->save($block);
        }
    }
}
