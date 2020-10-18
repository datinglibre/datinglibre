<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use App\Entity\ImageProjection;
use App\Entity\User;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Aws\S3\S3Client;
use DateInterval;
use DateTime;
use Ramsey\Uuid\UuidInterface;

class ImageService
{
    private ImageRepository $imageRepository;
    private UserRepository $userRepository;
    private S3Client $s3Client;
    private string $imagesBucket;

    public function __construct(
        string $imagesBucket,
        S3Service $s3Service,
        ImageRepository $imageRepository,
        UserRepository $userRepository
    ) {
        $this->imageRepository = $imageRepository;
        $this->userRepository = $userRepository;
        $this->s3Client = $s3Service->getClient();
        $this->imagesBucket = $imagesBucket;
    }

    public function save(UuidInterface $userId, $payload, string $type, bool $isProfile): Image
    {
        $user = $this->userRepository->find($userId);

        // only support one image at the moment
        $this->deleteByUserId($user->getId());

        $image = new Image();
        $image->setUser($user);
        $image->setType($type);
        $image->setIsProfile($isProfile);
        $image = $this->imageRepository->save($image);
        $this->sendToS3($image, $payload);

        $command = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->imagesBucket,
            'Key' => $image->getFilename()
        ]);

        // pre-signed URLs max expiry is 7 days, make it a bit earlier
        $expiry = (new DateTime())->add(new DateInterval('P6D'));
        $secureUrl = $this->s3Client->createPresignedRequest($command, $expiry);
        $image->setSecureUrl((string) $secureUrl->getUri());
        $image->setSecureUrlExpiry($expiry);

        return $this->imageRepository->save($image);
    }

    public function findUnmoderated(): ?ImageProjection
    {
        return $this->imageRepository->findUnmoderated();
    }

    public function accept(string $id)
    {
        $this->saveState($id, Image::ACCEPTED);
    }

    public function reject(string $id)
    {
        $this->saveState($id, Image::REJECTED);
    }

    public function saveState(string $id, string $state): void
    {
        $image = $this->imageRepository->find($id);

        if ($image === null) {
            return;
        }

        $image->setState($state);
        $this->imageRepository->save($image);
    }

    public function delete(string $bucket, Image $image): void
    {
        $this->s3Client->deleteObject([
                'Bucket' => $bucket,
                'Key' => $image->getFilename()
            ]);

        $this->imageRepository->delete($image);
    }

    public function deleteByUserId(?UuidInterface $userId): void
    {
        $image = $this->imageRepository->findOneBy(['user' => $userId]);
        if ($image !== null) {
            $this->delete($this->imagesBucket, $image);
        }
    }

    private function sendToS3(Image $image, $payload): void
    {
        $this->s3Client->putObject([
            'Bucket' => $this->imagesBucket,
            'Key' => $image->getFilename(),
            'Body' => $payload
        ]);
    }

    public function findProfileImageProjection(UuidInterface $userId)
    {
        return $this->imageRepository->findProjection($userId, true);
    }
}
