<?php

declare(strict_types=1);

namespace unit;

use GigaDB\services\MailerService;
use GigaDB\services\TokenService;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class MailerServiceTest extends TestCase
{
    public function testRenderNotificationEmailBody()
    {
        $config = [
            'template_path' => '/var/www/files/templates',
        ];

        $httpClient = $this->createMock(Client::class);
        $tokenService = $this->createMock(TokenService::class);
        $mailerService = new MailerService($httpClient, $tokenService, "test@test.fr", $config);
        $renderedContent = $mailerService->renderNotificationEmailBody('DataAvailableForReview', 100006);

;        $this->assertTrue(1 === preg_match('/dataset with DOI 100006/', $renderedContent));
    }
}
