<p align="center">
    <a href="http://www.serendipityhq.com" target="_blank">
        <img src="http://www.serendipityhq.com/assets/open-source-projects/Logo-SerendipityHQ-Icon-Text-Purple.png">
    </a>
</p>

SHQ USERS BUNDLE
================

[![PHP from Packagist](https://img.shields.io/packagist/php-v/serendipity_hq/bundle-users?color=%238892BF)](https://packagist.org/packages/serendipity_hq/bundle-users)
[![Tested with Symfony ^3.0](https://img.shields.io/badge/Symfony-%5E3.0-333)](https://github.com/Aerendir/bundle-users/actions)
[![Tested with Symfony ^4.0](https://img.shields.io/badge/Symfony-%5E4.0-333)](https://github.com/Aerendir/bundle-users/actions)
[![Tested with Symfony ^5.0](https://img.shields.io/badge/Symfony-%5E5.0-333)](https://github.com/Aerendir/bundle-users/actions)

[![Latest Stable Version](https://poser.pugx.org/serendipity_hq/bundle-users/v/stable.png)](https://packagist.org/packages/serendipity_hq/bundle-users)
[![Total Downloads](https://poser.pugx.org/serendipity_hq/bundle-users/downloads.svg)](https://packagist.org/packages/serendipity_hq/bundle-users)
[![License](https://poser.pugx.org/serendipity_hq/bundle-users/license.svg)](https://packagist.org/packages/serendipity_hq/bundle-users)

[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_bundle-users&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_bundle-users)

![Phan](https://github.com/Aerendir/bundle-users/workflows/Phan/badge.svg)
![PHPStan](https://github.com/Aerendir/bundle-users/workflows/PHPStan/badge.svg)
![PSalm](https://github.com/Aerendir/bundle-users/workflows/PSalm/badge.svg)
![PHPUnit](https://github.com/Aerendir/bundle-users/workflows/PHPunit/badge.svg)
![Composer](https://github.com/Aerendir/bundle-users/workflows/Composer/badge.svg)
![PHP CS Fixer](https://github.com/Aerendir/bundle-users/workflows/PHP%20CS%20Fixer/badge.svg)
![Rector](https://github.com/Aerendir/bundle-users/workflows/Rector/badge.svg)

A bundle that helps managing users in Symfony apps.

This bundle tries to avoid errors done by FOSUserBundle.

FOSUserBundle is more lke a plugin than a framework that makes easier to manage users.

FOSUserBundle does a lot of assumptions and forces you to flow its paths.

Read this for more informations about the problems of FOSUserBundle that this bundles tries to avoid: https://jolicode.com/blog/do-not-use-fosuserbundle

# Documentation

- Bootstrapping users management in your app (link to Symfony documentation)
- Installation of the SHQUsersBundle
- The `UsersManager` (provided by SHQUsersBundle)
- Events (The events triggered by the bundle: they are in src/Events)
- Commands (Explanation of the commands available) (How to extend commands)
- Creating a register form
- Creating a login form
- Creating a password reset form

## Configuring the `userInterface` entity

Implement `HasPlainPassword` and `use HasPlainPasswordTrait`.

This is will activate the Doctrine listener that will automatically encode the passwords properly.

## Creating a User with the manager

Method `create()` dispatches an event `UserCreatedEvent`.

You can listen for it and, if you like, you can set `UserCreatedEvent::stopPropagation()`.

If `true === UserCreatedEvent::isPropagationStopped()`, then the menager will not persist the user in the database, but will anyway return it.

This way you can decide what to do: create a new user, persist it by yourself, etc.

The manager will never call `EntityManager::flush()`: it is always your responsibility to call it and decide if and when to do so.

The method `UserInterface::eraseCredentials()` is never called by the bundle: it is a responsibility of yours as we don't know if you need them one more time (for example, to send an email to the registered user with the plain passowrd).

## Creating a registration form

When using the make command from Symfony, the generated form type has a field `plainPassword` that has the option `mapped = false`.

Implement the trait `HasPlainPassword` in the `User` entity and then remove the option from the form type.

This way, the `User` entity will have a field `plainPassword` provided by the trait, the form will bind the form field to this property and the Doctrine listener will automatically encode the password.

Also, modify the controller to not encode the password anymore.
