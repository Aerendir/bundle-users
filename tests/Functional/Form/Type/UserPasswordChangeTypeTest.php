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

use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Factories\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UserPasswordChangeTypeTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testPasswordChangeForm(): void
    {
        $client = self::createClient();

        /** @var User $user */
        $user = UserFactory::createOne([
            'email'    => 'test@example.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
        ]);

        $client->loginUser($user);

        $crawler = $client->request(Request::METHOD_GET, '/password-change');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Save')->form();

        // Test invalid old password
        $client->submit($form, [
            'user_password_change[old_password]'          => 'wrong_password',
            'user_password_change[plainPassword][first]'  => 'new_password123',
            'user_password_change[plainPassword][second]' => 'new_password123',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $bodyText = $client->getCrawler()->filter('body')->text();
        $this->assertTrue(
            str_contains($bodyText, 'This value should be the user\'s current password.')
            || str_contains($bodyText, 'form.error.old_password.passwords_mismatch'),
            sprintf('The error message for old password was not found in body. Body content: "%s"', $bodyText)
        );

        // Test password mismatch
        $crawler = $client->request(Request::METHOD_GET, '/password-change');
        $form    = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'user_password_change[old_password]'          => 'password123',
            'user_password_change[plainPassword][first]'  => 'new_password123',
            'user_password_change[plainPassword][second]' => 'mismatch',
        ]);
        $this->assertResponseStatusCodeSame(422);
        $bodyText = $client->getCrawler()->filter('body')->text();
        $this->assertTrue(
            str_contains($bodyText, 'form.password_change.new_password.error.mismatch')
            || str_contains($bodyText, 'This value is not valid.'),
            sprintf('The error message for password mismatch was not found in body. Body content: "%s"', $bodyText)
        );

        // Test valid submission
        $crawler = $client->request(Request::METHOD_GET, '/password-change');
        $form    = $crawler->selectButton('Save')->form();
        $client->submit($form, [
            'user_password_change[old_password]'          => 'password123',
            'user_password_change[plainPassword][first]'  => 'new_password123',
            'user_password_change[plainPassword][second]' => 'new_password123',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Form is valid');
    }
}
