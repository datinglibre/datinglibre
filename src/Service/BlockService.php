<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Block;
use App\Entity\BlockReason;
use App\Repository\BlockRepository;
use App\Repository\UserRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlockService
{
    private BlockRepository $blockRepository;
    private UserRepository $userRepository;

    public function __construct(BlockRepository $blockRepository, UserRepository $userRepository)
    {
        $this->blockRepository = $blockRepository;
        $this->userRepository = $userRepository;
    }

    public function block(
        UuidInterface $currentUserId,
        UuidInterface $userToBlockId,
        BlockReason $reason
    ) {
        $currentUser = $this->userRepository->find($currentUserId);
        $userToBlock = $this->userRepository->find($userToBlockId);

        if (null === $currentUser || null === $userToBlock) {
            throw new NotFoundHttpException("Could not find user to block");
        }

        $block = new Block();
        $block->setUser($currentUser);
        $block->setBlockedUser($userToBlock);
        $block->setReason($reason);

        $this->blockRepository->save($block);
    }
}
