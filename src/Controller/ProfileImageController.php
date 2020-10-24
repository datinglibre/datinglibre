<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ImageService;
use App\Service\ProfileService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileImageController extends AbstractController
{
    private ImageService $imageService;
    private ProfileService $profileService;
    private bool $disableImageUpload;

    public function __construct(
        ImageService $imageService,
        ProfileService $profileService,
        bool $disableImageUpload
    ) {
        $this->imageService = $imageService;
        $this->profileService = $profileService;
        $this->disableImageUpload = $disableImageUpload;
    }

    /**
     * @Route("/profile/image", name="profile_image")
     */
    public function index(Request $request)
    {
        $userId = $this->getUser()->getId();

        if ($request->isMethod('POST')) {
            if ($this->disableImageUpload) {
                throw $this->createAccessDeniedException();
            }

            $image = $request->files->get('image', null);

            if ($image != null) {
                $this->imageService->save($userId, file_get_contents($image->getRealPath()), 'jpg', true);
            }
        }

        return $this->render('profile/image.html.twig', [
            'profile' => $this->profileService->findProjection($userId),
            'disableImageUpload' => $this->disableImageUpload,
            'controller_name' => 'ProfileImageController'
        ]);
    }
}
