<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\User;
use App\Form\FilterFormType;
use App\Form\RequirementsForm;
use App\Form\RequirementsFormType;
use App\Repository\FilterRepository;
use App\Repository\UserRepository;
use App\Service\ProfileService;
use App\Service\RequirementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SearchIndexController extends AbstractController
{
    const PREVIOUS = 'previous';
    const NEXT = 'next';
    const LIMIT = 10;
    private ProfileService $profileService;
    private UserRepository $userRepository;
    private FilterRepository $filterRepository;
    private RequirementService $requirementService;

    public function __construct(
        ProfileService $profileService,
        UserRepository $userRepository,
        FilterRepository $filterRepository,
        RequirementService $requirementService
    ) {
        $this->profileService = $profileService;
        $this->userRepository = $userRepository;
        $this->filterRepository = $filterRepository;
        $this->requirementService = $requirementService;
    }

    /**
     * @Route("/search", name="search")
     */
    public function index(UserInterface $user, Request $request)
    {
        if (null === $this->profileService->find($user->getId())) {
            $this->addFlash('warning', 'profile.incomplete');
            return new RedirectResponse($this->generateUrl('profile_edit'));
        }

        $user = $this->userRepository->find($this->getUser()->getId());
        $profile = $this->profileService->find($user->getId());
        $filter = $this->filterRepository->find($user->getId()) ?? $this->createDefaultFilter($user);

        $filterForm = $this->createForm(
            FilterFormType::class,
            $filter,
            ['regions' => $profile->getCity()->getRegion()->getCountry()->getRegions()]
        );

        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $this->filterRepository->save($filter);
            return new RedirectResponse($this->generateUrl('search'));
        }

        $previous = (int) $request->query->get(self::PREVIOUS, 0);
        $next = (int) $request->query->get(self::NEXT, 0);

        $profiles = $this->profileService->findByLocation(
            $user->getId(),
            $filter->getDistance(),
            empty($filter->getRegion()) ? null : $filter->getRegion()->getId(),
            $filter->getMinAge(),
            $filter->getMaxAge(),
            $previous,
            $next,
            self::LIMIT
        );

        return $this->render('search/index.html.twig', [
            'next' => $this->getNext($profiles, $previous),
            'previous' => $this->getPrevious($profiles, $next),
            'page' => 'search',
            'profiles' => $profiles,
            'filterForm' => $filterForm->createView()
        ]);
    }

    public function createDefaultFilter(User $user): Filter
    {
        $filter = new Filter();
        $filter->setUser($user);
        return $this->filterRepository->save($filter);
    }

    private function getPrevious(array &$profiles, ?int $next): array
    {
        if ($next !== 0) {
            return [self::PREVIOUS => $next - 1];
        }

        if (count($profiles) === self::LIMIT + 1) {
            $previous = array_shift($profiles);
            return [self::PREVIOUS => $previous->getSortId()];
        }

        return [];
    }

    private function getNext(array &$profiles, ?int $previous): array
    {
        if ($previous !== 0) {
            return [self::NEXT => $previous + 1];
        }

        if (count($profiles) === self::LIMIT + 1) {
            $next = array_pop($profiles);
            return [self::NEXT => $next->getSortId()];
        }

        return [];
    }
}
