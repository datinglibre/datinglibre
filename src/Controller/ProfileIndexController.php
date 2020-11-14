<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProfileRepository;
use App\Service\UserAttributeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProfileIndexController extends AbstractController
{
    private ProfileRepository $profileRepository;
    private UserAttributeService $userAttributeService;

    public function __construct(
        ProfileRepository $profileRepository,
        UserAttributeService $userAttributeService
    ) {
        $this->profileRepository = $profileRepository;
        $this->userAttributeService = $userAttributeService;
    }

    /**
     * @Route("/profile", name="profile_index")
     */
    public function index()
    {
        $profile = $this->profileRepository->findProjection($this->getUser()->getId());

        if (null == $profile) {
            $this->addFlash('warning', 'profile.incomplete');
            return new RedirectResponse($this->generateUrl('profile_edit'));
        }

        return $this->render('profile/index.html.twig', [
            'attributes' => $this->userAttributeService->getAttributesByUser($profile->getId()),
            'profile' => $profile,
            'controller_name' => 'ProfileController',
        ]);
    }
}
