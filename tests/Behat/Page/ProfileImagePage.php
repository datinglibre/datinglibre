<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ProfileImagePage extends SymfonyPage
{
    const IS_UPLOADED_PROFILE_IMAGE_VISIBLE = '$("#profileImageSuccessMessage").is(":visible");';

    public function getRouteName(): string
    {
        return "profile_image";
    }

    public function uploadImage(string $image)
    {
        $imageUpload = $this->getElement('imageUpload');
        $imageUpload->attachFileToField('#uploadImageButton', 'cat.jpg');
    }

    public function waitUntilImageUploaded()
    {
        $this->getSession()->wait(5000, self::IS_UPLOADED_PROFILE_IMAGE_VISIBLE);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'imageUpload' => '#uploadImageButton'
        ]);
    }
}
