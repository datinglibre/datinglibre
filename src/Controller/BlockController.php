<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\BlockFormType;
use App\Service\BlockService;
use App\Service\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class BlockController extends AbstractController
{
    /**
     * @Route("/block/{userId}", name="block_user")
     */
    public function block(
        Uuid $userId,
        Request $request,
        ProfileService $profileService,
        BlockService $blockService
    ) {
        $profile = $profileService->findProjectionByCurrentUser($this->getUser()->getId(), $userId);

        if (null === $profile) {
            throw $this->createNotFoundException();
        }

        $blockFormType = $this->createForm(BlockFormType::class);
        $blockFormType->handleRequest($request);

        if ($blockFormType->isSubmitted() && $blockFormType->isValid()) {
            $blockService->block($this->getUser()->getId(), $userId, $blockFormType->getData()['reason']);

            $this->addFlash('success', 'block.success');
            return $this->redirectToRoute('search');
        }

        return $this->render(
            'block/create.html.twig',
            [
                'controller_name' => 'BlockController',
                'blockForm' => $blockFormType->createView(),
                'profile' => $profile
            ]
        );
    }
}
