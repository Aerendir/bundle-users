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

final class PasswordResetTypeTest extends WebTestCase
{
    public function testPasswordResetForm(): void
    {
        $client = self::createClient();

        $crawler = $client->request(Request::METHOD_GET, '/password-reset');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Submit')->form();

        // Test empty submission (NotBlank constraint in ConfirmedPasswordType)
        $client->submit($form, [
            'password_reset[plainPassword][first]'  => '',
            'password_reset[plainPassword][second]' => '',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('body', 'form.error.confirmed_password.not_blank');

        // Test password mismatch
        $crawler = $client->request(Request::METHOD_GET, '/password-reset');
        $form    = $crawler->selectButton('Submit')->form();
        $client->submit($form, [
            'password_reset[plainPassword][first]'  => 'new_password123',
            'password_reset[plainPassword][second]' => 'mismatch',
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('body', 'form.password_change.new_password.error.mismatch');

        // Test valid submission
        $crawler = $client->request(Request::METHOD_GET, '/password-reset');
        $form    = $crawler->selectButton('Submit')->form();
        $client->submit($form, [
            'password_reset[plainPassword][first]'  => 'new_password123',
            'password_reset[plainPassword][second]' => 'new_password123',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Form is valid');
    }
}
