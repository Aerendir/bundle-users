<p align="center">
    <a href="http://www.serendipityhq.com" target="_blank">
        <img style="max-width: 350px" src="http://www.serendipityhq.com/assets/open-source-projects/Logo-SerendipityHQ-Icon-Text-Purple.png">
    </a>
</p>

<h1 align="center">Serendipity HQ Users Bundle</h1>
<p align="center">Helps managing users in Symfony apps.</p>
<p align="center">
    <a href="https://github.com/Aerendir/bundle-users/releases"><img src="https://img.shields.io/packagist/v/serendipity_hq/bundle-users.svg?style=flat-square"></a>
    <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
    <a href="https://github.com/Aerendir/bundle-users/releases"><img src="https://img.shields.io/packagist/php-v/serendipity_hq/bundle-users?color=%238892BF&style=flat-square&logo=php" /></a>
</p>
<p>
    Supports:
    <a title="Supports Symfony ^5.4" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Supports Symfony ^5.4" src="https://img.shields.io/badge/Symfony-%5E5.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Supports Symfony ^6.0" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Supports Symfony ^6.0" src="https://img.shields.io/badge/Symfony-%5E6.0-333?style=flat-square&logo=symfony" /></a>
</p>
<p>
    Tested with:
    <a title="Tested with Symfony ^5.4" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Tested with Symfony ^5.4" src="https://img.shields.io/badge/Symfony-%5E5.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Tested with Symfony ^6.0" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Tested with Symfony ^6.0" src="https://img.shields.io/badge/Symfony-%5E6.0-333?style=flat-square&logo=symfony" /></a>
</p>

## Current Status

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)

[![Phan](https://github.com/Aerendir/bundle-users/workflows/Phan/badge.svg)](https://github.com/Aerendir/bundle-users/actions?query=branch%3Adev)
[![PHPStan](https://github.com/Aerendir/bundle-users/workflows/PHPStan/badge.svg)](https://github.com/Aerendir/bundle-users/actions?query=branch%3Adev)
[![PSalm](https://github.com/Aerendir/bundle-users/workflows/PSalm/badge.svg)](https://github.com/Aerendir/bundle-users/actions?query=branch%3Adev)
[![PHPUnit](https://github.com/Aerendir/bundle-users/workflows/PHPunit/badge.svg)](https://github.com/Aerendir/bundle-users/actions?query=branch%3Adev)
[![Composer](https://github.com/Aerendir/bundle-users/workflows/Composer/badge.svg)](https://github.com/Aerendir/bundle-users/actions?query=branch%3Adev)
[![PHP CS Fixer](https://github.com/Aerendir/bundle-users/workflows/PHP%20CS%20Fixer/badge.svg)](https://github.com/Aerendir/bundle-users/actions?query=branch%3Adev)
[![Rector](https://github.com/Aerendir/bundle-users/workflows/Rector/badge.svg)](https://github.com/Aerendir/bundle-users/actions?query=branch%3Adev)

## Features

Provides some utilities to make easier the management of users in Symfony applications, on top of Symfony's built-in management of users.

<hr />
<h3 align="center">
    <b>Do you like this bundle?</b><br />
    <b><a href="#js-repo-pjax-container">LEAVE A &#9733;</a></b>
</h3>
<p align="center">
    or run<br />
    <code>composer global require symfony/thanks && composer thanks</code><br />
    to say thank you to all libraries you use in your current project, this included!
</p>
<hr />

# Documentation

The starting point is always the Symfony's documentation.

- [Bootstrapping users management in your app](https://symfony.com/doc/current/security.html)
- [How to build a login form](https://symfony.com/doc/current/security/form_login_setup.html)

Once you have configured the `UserInterface` entity, configured the security of your app and built the login form, it's time to create your first user, even before you build the registration form.

To make users management easier, `SerendipityHQ Users Bundle` provides a command `shq:users:create` that permits to create users from the command line.

It works almost out of the box: you only need to tweak just a bit the entity automatically generated by Symfony.

## Install the Serendipity HQ Users Bundle

To install the bundle, run:

    composer req serendipity_hq/bundle-users

Then activate the bundle in your `bundles.php`:

```diff
<?php

// config/bundles.php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
+    SerendipityHQ\Bundle\UsersBundle\SHQUsersBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
];
```

## Using the `shq:users:create` command

This command is very useful as it permits you to create users in your application before you have created the registration form.

This way you can immediately test the login functionality, but can also automate some tasks in your app, like resetting it in dev environment, without having to create a new user each time from the registration form.

To use the command, you have to do one simple thing: implement the `HasPlainPasswordInterface` and its implementing trait in your `UserInterface` entity.

The `HasPlainPasswordInterface` makes possible to get a series of advantages: we will see them later.

For the moment be sure that you will NEVER save the plain password in the database: it is only useful during the life cycle of a `UserInterface` object and permits to your app to implement some basic features.

For the moment, lets implement the interface and the trait.

1. Open you `UserInterface` entity (`src/App/Entity/User.php`);
2. Implement the interface `\SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface`
3. Use the trait `SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordTrait`

After these modifications, your entity should appear like this:

```diff
<?php

declare(strict_types = 1);

/*
 * This file is part of Trust Back Me.
 *
 * Copyright (c) Adamo Aerendir Crespi <hello@aerendir.me>.
 *
 * This code is to consider private and non disclosable to anyone for whatever reason.
 * Every right on this code is reserved.
 *
 * For the full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
+ use SerendipityHQ\Bundle\UsersBundle\Property\HasPlainPasswordInterface;
+ use SerendipityHQ\Bundle\UsersBundle\Property\HasPlainPasswordTrait;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="tbme_users")
 */
+ class User implements UserInterface, HasPlainPasswordInterface
{
+    use HasPlainPasswordTrait;

    // Here the remaining code of your entity
    // ...
}
```

Now create your first user using the command line:

    Aerendir@SerendipityHQ % bin/console shq:user:create Aerendir 1234

    Create user
    ===========

    Password for user Aerendir: 1234


     [OK] User Aerendir created.


`Aerendir` (the first argument), is the value of the primary property (the one set in the file `config/packages/security.yaml`, in `security.providers.[your_user_provider].entity.property`);
`1234` (the second argument), instead, is the password to assign to the user.

You are ready to test the login of your app: go to `http://your_app_url/login` and provide the credentials of the user you have just created.

Now that your login works, we can go further better understanding the purpose of `HasPlainPasswordInterface`.

## The purpose of `HasPlainPasswordInterface`

The interface `HasPlainPasswordInterface` activates a Doctrine's listener provided by Serendipity HQ Users Bundle.

This listener reads the plain password (managed by the trait `HasPlainPasswordTrait`) and automatically encodes it.

This way you will have the plain password available during the life cycle of the user object and:

1. You don't have to take care of encryption of the password;
2. You can use the plain password to do what you like: send it via email to the user, show it on a page or do what you like.

**ATTENTION: Serendipity HQ Users Bundle will never call the method `UserInterface::ereaseCredentials()`: this is a responsibility of your app.**

## Implementing a profile show page

The next step is to make possible for the user to view his/her profile.

You need:

1. A `UserProfileController`
2. A template to show the current profile

### 1. Create the `UserProfileController`

```php
<?php

// src/Controller/UserProfileController.php

declare(strict_types = 1);

/*
 * This file is part of Trust Back Me.
 *
 * Copyright (c) Adamo Aerendir Crespi <hello@aerendir.me>.
 *
 * This code is to consider private and non disclosable to anyone for whatever reason.
 * Every right on this code is reserved.
 *
 * For the full copyright and license information, please view the LICENSE file that
 * was distributed with this source code.
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER')", statusCode=403)
 */
final class UserProfileController extends AbstractController
{
    /**
     * @Route("/me/", name="user_profile")
     */
    public function show(): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
```

### 2. Create the template `user/profile.html.twig`

And this is the code for the template that renders the profile:

```twig
{# templates/user/profile.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}Your profile{% endblock %}

{% block body %}
    <h1 class="text-center">Hello {{ user.username }}</h1>
{% endblock %}

```

This is really simple, too, as you will customize it depending on your application.

## Implementing a profile edit page

To create an edit profile page we need:

1. A `UserType` form type
2. A route in the `UserProfileController`
2. A template to show the form

### Create the `UserType` form type

Use the MakerBundle to do this:

```console
Aerendir@Archimede bundle-users % bin/console make:form

 The name of the form class (e.g. VictoriousPizzaType):
 > UserType

 The name of Entity or fully qualified model class name that the new form will be bound to (empty for none):
 > User

 created: src/Form/UserType.php


  Success!


 Next: Add fields to your form and start using it.
 Find the documentation at https://symfony.com/doc/current/forms.html

```

Really simple and it takes only a bunch of seconds!

### 2. Create the route in the `UserProfileController`

Lets use the just crated form type in our `UserProfileController`:

```diff
// src/Controller/UserProfileController.php

+ use App\Form\UserType;

final class UserProfileController extends AbstractController
{
    ...

+    /**
+     * @Route("/profile/edit", name="user_profile_edit")
+     */
+    public function edit(Request $request): Response
+    {
+        /** @var User $user */
+        $user = $this->getUser();
+        $form = $this->getFormFactory()->create(UserType::class, $user, [
+            'action' => $this->generateUrl('user_profile_edit'),
+            'method' => 'POST',
+        ]);
+
+        $form->handleRequest($request);
+
+        if ($form->isSubmitted() && $form->isValid()) {
+            $this->getDoctrine()->getManager()->flush();
+            $url = $this->generateUrl('user_profile');
+
+            return new RedirectResponse($url);
+        }
+
+        return $this->render('user/edit.html.twig', [
+            'form' => $form->createView(),
+        ]);
+    }
...
}
```

### 3. Create the template

Create the file `templates/user/edit.html.twig`:

```twig
{# templates/user/edit.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}Edit your profile{% endblock %}

{% block body %}
    {{ form(form) }}
{% endblock %}
```

Really simple!

## Implementing password editing

Now we are going to start to see the usefulness of Symfony HQ Users Bundle!

We need to implement the ability for the user of changing his/her own password.

To implement this we need:

1. A form to make possible for the user to change his/her password
2. A `UserPasswordController`
3. A template to show the form

### 1. Create the form `ChangePasswordType`

Hey, you have to do nothing here!

Serendipity HQ Users Bundle comes with a prebuilt form to change the password.

Find it here: [src/Form/Type/UserPasswordChangeType.php](src/Form/Type/UserPasswordChangeType.php)

It provides three fields:

1. `old_password`
2. `plainPassword`
3. Confirmation of `plainPassword`

Under the hood, it uses the `RepeatedType` provided by Symfony to ensure that the new password and confirmation password are equals.

And thanks to the interface `HasPlainPasswordInterface`, the form can automatically be handled by Serendipity HQ Users Bundle.

All is really easy!

Now we need a route.

### 2. Create the `UserPasswordController::changePassword()`

As usual, use the `make` command:

```console
Aerendir@Archimede bundle-users % symfony console make:controller

 Choose a name for your controller class (e.g. AgreeablePizzaController):
 > UserPasswordController

 created: src/Controller/UserPasswordController.php
 created: templates/user_password/index.html.twig


  Success!


 Next: Open your new controller class and add some pages!

```

The command created both the controller and its template.

Open the controller and remove the `index()` route.

Then add the route `changePassword()`:

```diff
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
+ use SerendipityHQ\Bundle\UsersBundle\Manager\PasswordManager;
+ use SerendipityHQ\Bundle\UsersBundle\Property\HasPlainPasswordInterface;
+ use SerendipityHQ\Bundle\UsersBundle\SHQUsersBundle;
+ use Symfony\Component\HttpFoundation\RedirectResponse;
+ use Symfony\Component\HttpFoundation\Request;
+ use Symfony\Component\HttpFoundation\Response;
+ use Symfony\Contracts\Translation\TranslatorInterface;

class UserPasswordController extends AbstractController
{
+    private PasswordManager $passwordManager;
+
+    public function __construct(PasswordManager $passwordManager)
+    {
+        $this->passwordManager = $passwordManager;
+    }

-    /**
-     * @Route("/user/password", name="user_password")
-     */
-    public function index()
-    {
-        return $this->render('user_password_test/index.html.twig', [
-            'controller_name' => 'UserPasswordTestController',
-        ]);
-    }

+    /**
+     * @Route("/profile/password", name="user_password_change")
+     * @Security("is_granted('ROLE_USER')", statusCode=403)
+     */
+    public function changePassword(Request $request, TranslatorInterface $translator): Response
+    {
+        /** @var HasPlainPasswordInterface $user */
+        $user = $this->getUser();
+        $form = $this->passwordManager->getPasswordHelper()->createFormPasswordChange($user);
+
+        $form->handleRequest($request);
+
+        if ($form->isSubmitted() && $form->isValid()) {
+            $this->getDoctrine()->getManager()->flush();
+            $this->addFlash('success', $translator->trans('user.password.change_password.success', [], 'shq_users'));
+            $url = $this->generateUrl('user_profile');
+
+            return new RedirectResponse($url);
+        }
+
+        return $this->render('user/password/password_change.html.twig', [
+            'form' => $form->createView(),
+        ]);
+    }
}
```

There are some things you should note here:

1. The use of annotation `@Security`: in case the current user is not logged in, the route will return a `Symfony\Component\Security\Core\Exception\AccessDeniedException`;
2. The name of the route MUST be the same of the one in [Routes::PASSWORD_CHANGE](src/Routes.php);
3. The variable `$user` is indicated as of type `HasPlainPasswordInterface`: the method `PasswordHelper::createFormPasswordChange()`, in fact, accept a variable of type `HasPlainPasswordInterface`;
4. If the form is submitted and is valid, we simply call `EntityManagerInterface::flush()`: the form, in fact, will automatically update the `$user` with the new plain password provided;

All is ready to be used: now your users are able to change their passwords!

Remember to add the links to this page somewhere in your app (typically, in the profile page or in a user menu).

Now we need to make possible for the users to reset their passwords in case they have forgot them.

## Implementing password resetting

The flow to reset a password is as follows:

1. Show a page where the user can provide his/her main identifier (username, email, phone number, etc.);
2. Generate a unique token and send it to the user (via email, SMS or any other channel you like)
3. Validate the token and show the user a form that permits to set a new password.

In this example we will assume the following:

1. The unique identifier of the user is the email;
2. The token will be sent via email.

To implement the entire flow we need:

1. Three routes to manage the three steps (reset request, link sent confirmation, reset page)
2. The corresponding templates (for the routes and for the email)
3. An entity that represents the token
4. A listener to send the email

Lets start!

### Create the reset request

To make possible to request a reset token, we need:

1. A form type
2. A route
3. A template

Start by adding to the `UserPasswordController` the method `resetRequest()`:

```php
// src/Controller/
class UserPasswordController extends AbstractController
{
    /**
     * @Route("password-reset", name="user_password_reset_request")
     */
    public function resetRequest(Request $request): Response
    {
        $form = $this->passwordManager->getPasswordHelper()->createFormPasswordResetRequest();

        $form->handleRequest($request);

        // Listen for the event PasswordResetTokenCreatedEvent or for the event PasswordResetTokenCreationFailedEvent
        // If the user was found, then process the request.
        // If the user is not found, do nothing to avoid disclosing if
        // the user exists or not (for security)
        if (
            $form->isSubmitted() &&
            $form->isValid() &&
            $this->passwordManager->handleResetRequest($request, $form)
        ) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_password_reset_check_email');
        }

        return $this->render('App/user/password/reset_request.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
```

As you can see, the route is really simple.

There are some things you should note here:

1. The name of the route MUST be the same of the one in [Routes::PASSWORD_RESET_REQUEST](src/Routes.php);
2. Once we are sure the form hasn't any error, we handle the request through Serendipity HQ Users Bundle and then we simply flush the changes to the database: the bundle, in fact, takes care of all the operations: we only need to flush them (Remember: the bundle will never flush Doctrine!).

Also note that we don't have to build any form: it is built by Serendipity HQ Users Bundle.

Now we need to create the template: this is something really customized on the app, so we need to create it on our own.

```twig
{# templates/user/password/reset_request.html.twig #}
<h1>Reset your password</h1>

{{ form_start(form) }}
    {{ form_row(form.primaryEmail) }}
    <div>
        <small>
            Enter your email address and we we will send you a
            link to reset your password.
        </small>
    </div>

    <button class="btn btn-primary">Send password reset email</button>
{{ form_end(form) }}
```

We are ready.

Now its time to process this request.

### Create the confirmation page

Create the method `UserPasswordController::resetRequestReceived()`:

```php
    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/password-reset/requested", name="user_password_reset_request_received")
     */
    public function resetRequestReceived(Request $request): Response
    {
        // Prevent users from directly accessing this page
        if (!$this->passwordManager->getPasswordResetHelper()->canAccessPageCheckYourEmail($request)) {
            return $this->redirectToRoute('user_password_reset_request');
        }

        return $this->render('App/user/password/check_email.html.twig', [
            'tokenLifetime' => PasswordResetHelper::RESET_TOKEN_LIFETIME,
        ]);
    }
```

This method is really simple, too.

The only thing you have to note is the call to `canAccessPageCheckYourEmail`: this method will read the session of the current (anonymous) user and checks if there is a value previously set in `UserPasswordController::resetRequest()` by calling the `PasswordManager::handleResetRequest()`.

Now we need to create the last route, the one that will actually make the user able to set his/her new password.

### Create the reset page

Create the method `UserPasswordController::reset()`:

```php
    /**
     * @Route("/password-reset/reset/{token}", name="user_password_reset_reset_password")
     */
    public function reset(Request $request, EncoderFactoryInterface $passwordEncoderFactory, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->passwordManager->getPasswordResetHelper()->storeTokenInSession($request, $token);

            return $this->redirectToRoute('user_password_reset_reset_password');
        }

        $token = $this->passwordManager->getPasswordResetHelper()->getTokenFromSession($request);
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /**
             * findUserByPublicToken also validates the token and throws exceptions on failed validation.
             * @var HasPlainPasswordInterface $user
             */
            $user = $this->passwordManager->findUserByPublicToken($token);
        } catch (PasswordResetException $e) {
            $this->addFlash('user_password_reset_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getMessage()
            ));

            return $this->redirectToRoute('user_password_reset_request');
        }

        $form = $this->passwordManager->getPasswordHelper()->createFormPasswordReset();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->passwordManager->handleReset($token, $user, $form, $request);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('auth_login');
        }

        return $this->render('App/user/password/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
```

This method is a bit longer than the others: it has to do a lot of things!

The code is commented, so it is clear what it does.

The only thing to note is, again, the call to the method `EntityManager::flush()`: again, the bundle will never flush the database: this is a responsibility of ours.

If you try to reset your password now, you will receive an error and, anyway, there is no code that actually sends the token to the user.

We need to implment those two missing pieces.

### Creating the `PasswordResetToken` entity

First, we need to create the entity that will represent the reset token.

Create the entity `PasswordResetToken`:

```php
// src/Entity/PasswordResetToken.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenTrait;
use SerendipityHQ\Bundle\UsersBundle\Repository\PasswordResetTokenRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=PasswordResetTokenRepository::class)
 * @ORM\Table(name="tbme_users_password_reset_tokens")
 */
class PasswordResetToken implements PasswordResetTokenInterface
{
    use PasswordResetTokenTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
```

This class MUST implement the interface `PasswordResetTokenInterface` and will be used by the method `handleResetRequest()` in `UserPasswordController::resetRequest()`.

You can customize it as you like.

The two things you may want to customize are:

1. Your `User` class in the `ManyToOne` relation;
2. The namespace of the entity itself.

In this second case, the default namespace used by the bundle is `App\Entity\PasswordResetToken`, but you can change it (and also the name of the entity class) by setting the parameter `shq_users.token_class` in your configuration.

### Creating the subscriber to send the email to the user

To actually send the email to the user, we need a subscriber that listens for the event `PasswordResetTokenCreatedEvent`.

Create it:

```php
<?php
// src/Subscriber/SecurityPasswordResetSubscriber.php

namespace App\Subscriber;

use SerendipityHQ\Bundle\UsersBundle\Event\PasswordResetTokenCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Send reminders to ask the feedback releasing.
 */
final class SecurityPasswordResetSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordResetTokenCreated'
        ];
    }

    public function onPasswordResetTokenCreated(PasswordResetTokenCreatedEvent $event)
    {
        $user = $event->getUser();
        $token = $event->getToken();

        $email = (new TemplatedEmail())
            // @todo Use values from ENV config
            ->from(new Address('hello@trustback.me', 'TrustBack.Me'))
            ->to($user->getEmail())
            // @todo translate this message
            ->subject('Your password reset request')
            ->htmlTemplate('App/user/password/reset_email.html.twig')
            ->context(['token' => $token]);

        $this->mailer->send($email);
    }
}
```

Now we are done!

Try to reset your password and all should work as expected.

In case something doesn't work, please, open an issue.

# Other useful features

- [How to add "Remember me" functionality](https://symfony.com/doc/current/security/remember_me.html)

- The `UsersManager` (provided by SHQUsersBundle)
- Events (The events triggered by the bundle: they are in src/Events)
- Commands (Explanation of the commands available) (How to extend commands)
- Creating a register form
- Creating a login form
- Creating a password reset form

## Creating a User with the manager

Method `create()` dispatches an event `UserCreatedEvent`.

You can listen for it and, if you like, you can set `UserCreatedEvent::stopPropagation()`.

If `true === UserCreatedEvent::isPropagationStopped()`, then the manager will not persist the user in the database, but will anyway return it.

This way you can decide what to do: create a new user, persist it by yourself, etc.

The manager will never call `EntityManager::flush()`: it is always your responsibility to call it and decide if and when to do so.

The method `UserInterface::eraseCredentials()` is never called by the bundle: it is a responsibility of yours as we don't know if you need them one more time (for example, to send an email to the registered user with the plain passowrd).

## Creating a registration form

When using the make command from Symfony, the generated form type has a field `plainPassword` that has the option `mapped = false`.

Implement the trait `HasPlainPassword` in the `User` entity and then remove the option from the form type.

This way, the `User` entity will have a field `plainPassword` provided by the trait, the form will bind the form field to this property and the Doctrine listener will automatically encode the password.

Also, modify the controller to not encode the password anymore.

## How to create a command to manage users

# Managing password reset

1. Create the repo `PasswordResetTokenRepository` and implement the interface `PasswordResetTokenRepositoryInterface`
2. Create the controller `PasswordController` (with which methods/routes?)

## Handling garbage collection



<hr />
<h3 align="center">
    <b>Do you like this bundle?</b><br />
    <b><a href="#js-repo-pjax-container">LEAVE A &#9733;</a></b>
</h3>
<p align="center">
    or run<br />
    <code>composer global require symfony/thanks && composer thanks</code><br />
    to say thank you to all libraries you use in your current project, this included!
</p>
<hr />
