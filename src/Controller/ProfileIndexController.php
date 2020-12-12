<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProfileRepository;
use App\Repository\RequirementRepository;
use App\Repository\UserAttributeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProfileIndexController extends AbstractController
{
    private ProfileRepository $profileRepository;
    private UserAttributeRepository $userAttributeRepository;
    private RequirementRepository $requirementRepository;

    public function __construct(
        ProfileRepository $profileRepository,
        UserAttributeRepository $userAttributeRepository,
        RequirementRepository $requirementRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->userAttributeRepository = $userAttributeRepository;
        $this->requirementRepository = $requirementRepository;
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
            'sex' => $this->userAttributeRepository->getOneByUserAndCategory($this->getUser()->getId(), 'sex'),
            'relationship' => $this->userAttributeRepository->getOneByUserAndCategory($this->getUser()->getId(), 'relationship'),
            'sexes' => $this->requirementRepository->getMultipleByUserAndCategory($this->getUser()->getId(), 'sex'),
            'profile' => $profile,
            'controller_name' => 'ProfileController'
        ]);
    }
}
