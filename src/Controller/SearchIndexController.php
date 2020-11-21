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
    const SORT_ID = 'sortId';
    const DATETIME_FORMAT = 'Y-m-dh:s';
    const PREVIOUS = 'previous';
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


        $requirements = new RequirementsForm();
        $requirements->setColors($this->requirementService->getMultipleByUserAndCategory($user->getId(), 'color'));
        $requirements->setShapes($this->requirementService->getMultipleByUserAndCategory($user->getId(), 'shape'));
        $requirementsForm = $this->createForm(RequirementsFormType::class, $requirements);

        $filterForm->handleRequest($request);
        $requirementsForm->handleRequest($request);

        if ($requirementsForm->isSubmitted() && $requirementsForm->isValid()) {
            $this->requirementService->createRequirementsInCategory(
                $user,
                'color',
                $requirementsForm->getData()->getColors()
            );

            $this->requirementService->createRequirementsInCategory(
                $user,
                'shape',
                $requirementsForm->getData()->getShapes()
            );
        }

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $this->filterRepository->save($filter);
            return new RedirectResponse($this->generateUrl('search'));
        }

        $sortId = (int) $request->query->get(self::SORT_ID, 0);
        $previous = (bool) $request->query->get(self::PREVIOUS, false);

        $profiles = $this->profileService->findByLocation(
            $user->getId(),
            $filter->getDistance(),
            empty($filter->getRegion()) ? null : $filter->getRegion()->getId(),
            $filter->getMinAge(),
            $filter->getMaxAge(),
            $previous,
            $sortId,
            self::LIMIT
        );

        return $this->render('search/index.html.twig', [
            'next' => $this->getNext($profiles, self::LIMIT),
            self::PREVIOUS => $this->getPrevious($profiles, $sortId),
            'page' => 'search',
            'profiles' => $profiles,
            'filterForm' => $filterForm->createView(),
            'requirementsForm' => $requirementsForm->createView()
        ]);
    }


    private function getNext(array $profiles, int $limit): array
    {
        // if not even enough profiles for this page
        if (!(count($profiles) === $limit)) {
            return [];
        }

        $lastProfile = $profiles[$limit - 1];

        return [self::SORT_ID => $lastProfile->getSortId()];
    }

    private function getPrevious(array $profiles, ?int $sortId): array
    {
        // if no profiles or sort ID, disable previous
        // this works for first load of the search page
        // it is a bug that it doesn't work when the
        // user navigates forwards, then backwards.
        if (count($profiles) === 0 || $sortId === 0) {
            return [];
        }

        // if this page didn't have any profiles use existing query params
        if (count($profiles) === 0) {
            return [self::SORT_ID => $sortId, self::PREVIOUS => true];
        }

        return [self::SORT_ID => $profiles[0]->getSortId(), self::PREVIOUS => true];
    }

    public function createDefaultFilter(User $user): Filter
    {
        $filter = new Filter();
        $filter->setUser($user);
        return $this->filterRepository->save($filter);
    }
}
