<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ImageService;
use App\Service\ProfileService;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileImageController extends AbstractController
{
    private ImageService $imageService;
    private ProfileService $profileService;
    private bool $imageUpload;
    private const HEIGHT = 255;
    private const WIDTH = 255;

    public function __construct(
        ImageService $imageService,
        ProfileService $profileService,
        bool $imageUpload
    ) {
        $this->imageService = $imageService;
        $this->profileService = $profileService;
        $this->imageUpload = $imageUpload;
    }

    /**
     * @Route("/profile/image", name="profile_image")
     * @throws ImageResizeException
     */
    public function index(Request $request)
    {
        $userId = $this->getUser()->getId();

        if ($request->isMethod('POST')) {
            if (!$this->imageUpload) {
                throw $this->createAccessDeniedException();
            }

            $image = $request->files->get('image', null);

            if ($image != null) {
                $image = new ImageResize($image->getRealPath());
                $image->resize(self::HEIGHT, self::WIDTH);
                $this->imageService->save($userId, $image->getImageAsString(), 'jpg', true);
            }
        }

        return $this->render('profile/image.html.twig', [
            'profile' => $this->profileService->findProjection($userId),
            'imageUpload' => $this->imageUpload,
            'controller_name' => 'ProfileImageController'
        ]);
    }
}
