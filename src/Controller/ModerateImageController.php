<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ModerateForm;
use App\Form\ModerateFormType;
use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ModerateImageController extends AbstractController
{
    const ACCEPT = 'accept';
    const REJECT = 'reject';
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @Route("/moderator/profile/images", name="moderate_profile_images")
     */
    public function index(Request $request)
    {
        $moderateFormData = new ModerateForm();
        $profileImageProjection = $this->imageService->findUnmoderated();

        $moderateForm = $this->createForm(ModerateFormType::class, $moderateFormData);

        $moderateForm->handleRequest($request);
        if ($moderateForm->isSubmitted() && $moderateForm->isValid()) {
            if ($moderateForm->get(self::ACCEPT)->isClicked()) {
                $this->imageService->accept($profileImageProjection->getId());
                $this->addFlash('success', 'moderate.accepted');
            } else {
                $this->addFlash('warning', 'moderate.rejected');
                $this->imageService->reject($profileImageProjection->getId());
            }

            return new RedirectResponse($this->generateUrl('moderate_profile_images'));
        }

        return $this->render('moderator/profile/images.html.twig', [
            'profileImage' => $profileImageProjection,
            'controller_name' => 'ModerateProfileImagesController',
            'moderateForm' => $moderateForm->createView()
        ]);
    }
}
