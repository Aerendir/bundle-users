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
    <a title="Tested with Symfony ^5.1" href="https://github.com/Aerendir/bundle-aws-ses-monitor/actions?query=branch%3Adev"><img title="Tested with Symfony ^5.1" src="https://img.shields.io/badge/Symfony-%5E5.1-333?style=flat-square&logo=symfony" /></a>
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

This bundle tries to avoid errors done by FOSUserBundle.

FOSUserBundle is more lke a plugin than a framework that makes easier to manage users.

FOSUserBundle does a lot of assumptions and forces you to flow its paths.

Read this for more informations about the problems of FOSUserBundle that this bundles tries to avoid: https://jolicode.com/blog/do-not-use-fosuserbundle

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
