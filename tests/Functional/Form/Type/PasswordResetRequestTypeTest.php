<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Users Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Functional\Form\Type;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class PasswordResetRequestTypeTest extends WebTestCase
{
    public function testPasswordResetRequestForm(): void
    {
        $client = self::createClient();

        $crawler = $client->request(Request::METHOD_GET, '/password-reset-request');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Submit')->form();

        // Test empty submission (NotBlank constraint)
        $client->submit($form, [
            'password_reset_request[email]' => '',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('body', 'form.error.primary.not_blank');

        // Test valid submission
        $crawler = $client->request(Request::METHOD_GET, '/password-reset-request');
        $form    = $crawler->selectButton('Submit')->form();
        $client->submit($form, [
            'password_reset_request[email]' => 'test@example.com',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Form is valid');
    }
}
