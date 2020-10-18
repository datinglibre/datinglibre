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

    public function __construct(ImageService $imageService, ProfileService $profileService)
    {
        $this->imageService = $imageService;
        $this->profileService = $profileService;
    }

    /**
     * @Route("/profile/image", name="profile_image")
     */
    public function index(Request $request)
    {
        $image = $request->files->get('image', null);
        $userId = $this->getUser()->getId();

        if ($image != null) {
            $this->imageService->save($userId, file_get_contents($image->getRealPath()), 'jpg', true);
        }

        return $this->render('profile/image.html.twig', [
            'profile' => $this->profileService->findProjection($userId),
            'controller_name' => 'ProfileImageController'
        ]);
    }
}
