<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileForm;
use App\Form\ProfileFormType;
use App\Repository\CountryRepository;
use App\Repository\ProfileRepository;
use App\Repository\RegionRepository;
use App\Repository\UserRepository;
use App\Service\MatchingService;
use App\Service\ProfileService;
use App\Service\UserAttributeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileEditController extends AbstractController
{
    private MatchingService $matcherService;
    private ProfileRepository $profileRepository;
    private UserRepository $userRepository;
    private RegionRepository $regionRepository;
    private CountryRepository $countryRepository;
    private UserAttributeService $userAttributeService;
    private ProfileService $profileService;

    public function __construct(
        MatchingService $matcherService,
        ProfileRepository $profileRepository,
        ProfileService $profileService,
        UserRepository $userRepository,
        RegionRepository $regionRepository,
        CountryRepository $countryRepository,
        UserAttributeService $userAttributeService
    ) {
        $this->profileRepository = $profileRepository;
        $this->matcherService = $matcherService;
        $this->userRepository = $userRepository;
        $this->regionRepository = $regionRepository;
        $this->countryRepository = $countryRepository;
        $this->userAttributeService = $userAttributeService;
        $this->profileService = $profileService;
    }

    /**
     * @Route("/profile/edit", name="profile_edit")
     */
    public function edit(Request $request)
    {
        $userId = $this->getUser();
        $user = $this->userRepository->find($userId);
        $profile = $this->profileRepository->find($userId) ?? (new Profile())->setUser($user);
        $profileProjection = $this->profileService->findProjection($user->getId());

        $profileForm = new ProfileForm();
        $city = $profile->getCity();

        if ($city != null) {
            $profileForm->setCountry($city->getCountry());
            $profileForm->setRegion($city->getRegion());
            $profileForm->setCity($city);
        }

        $profileForm->setAbout($profile->getAbout());
        $profileForm->setUsername($profile->getUsername());
        $profileForm->setDob($profile->getDob());
        $profileForm->setColor($this->userAttributeService->getByCategory($user, 'color'));
        $profileForm->setShape($this->userAttributeService->getByCategory($user, 'shape'));

        $profileFormType = $this->createForm(ProfileFormType::class, $profileForm);
        $profileFormType->handleRequest($request);

        if ($profileFormType->isSubmitted() && $profileFormType->isValid()) {
            $this->userAttributeService->save($user, $profileFormType->getData()->getColor());
            $this->userAttributeService->save($user, $profileFormType->getData()->getShape());

            $profile->setCity($profileFormType->getData()->getCity());
            $profile->setUsername($profileFormType->getData()->getUsername());
            $profile->setAbout($profileFormType->getData()->getAbout());
            $profile->setDob($profileFormType->getData()->getDob());
            $this->profileRepository->save($profile);
            return new RedirectResponse($this->generateUrl('profile_index'));
        }

        return $this->render('profile/edit.html.twig', [
            'controller_name' => 'ProfileEditController',
            'profileForm' => $profileFormType->createView(),
            'profile' => $profileProjection,
        ]);
    }
}
