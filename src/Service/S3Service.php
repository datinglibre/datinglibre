<?php

namespace App\Service;

use Aws\S3\S3Client;

class S3Service
{
    private string $endpoint;
    private string $accessKey;
    private string $secretKey;
    private string $region;
    private ?S3Client $s3Client;

    public function __construct(
        string $endpoint,
        string $accessKey,
        string $secretKey,
        string $region
    ) {
        $this->endpoint = $endpoint;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->region = $region;
        $this->s3Client = null;
    }

    public function getClient(): S3Client
    {
        if ($this->s3Client !== null) {
            return $this->s3Client;
        }

        $this->s3Client = new S3Client([
            'endpoint' => $this->endpoint,
            'use_path_style_endpoint' => true,
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => [
                'secret' => $this->secretKey,
                'key' => $this->accessKey
            ]
        ]);

        return $this->s3Client;
    }
}
