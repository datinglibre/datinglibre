<?php

declare(strict_types=1);

namespace App\Tests\Behat;

class MailHogConstants
{
    public const EMAIL_REST_URL = "http://%s:8025/api/v2/search?kind=to&query=%s&limit=1";
    public const DELETE_EMAILS_URL = "http://%s:8025/api/v1/messages";
}
