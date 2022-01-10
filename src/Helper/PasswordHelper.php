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

namespace SerendipityHQ\Bundle\UsersBundle\Helper;

use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordEncodingError;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordRequired;
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementUserInterface;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\PasswordResetRequestType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\PasswordResetType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\UserPasswordChangeType;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Routes;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\ByteString;

final class PasswordHelper
{
    private const ACTION = 'action';
    private const METHOD = 'method';
    private const POST   = 'POST';

    private string $secUserProperty;
    private PasswordHasherFactoryInterface $hasherFactory;
    private FormFactoryInterface $formFactory;
    private RouterInterface $router;

    public function __construct(
        string $secUserProperty,
        PasswordHasherFactoryInterface $hasherFactory,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        $this->secUserProperty = $secUserProperty;
        $this->hasherFactory   = $hasherFactory;
        $this->formFactory     = $formFactory;
        $this->router          = $router;
    }

    public function createFormPasswordChange(HasPlainPasswordInterface $user): FormInterface
    {
        $action = $this->router->generate(Routes::PASSWORD_CHANGE);

        return $this->formFactory->create(UserPasswordChangeType::class, $user, [
            self::ACTION => $action,
            self::METHOD => self::POST,
        ]);
    }

    public function createFormPasswordResetRequest(): FormInterface
    {
        $action = $this->router->generate(Routes::PASSWORD_RESET_REQUEST);

        return $this->formFactory->create(PasswordResetRequestType::class, null, [
            self::ACTION                                => $action,
            self::METHOD                                => self::POST,
            'allow_extra_fields'                        => true,
            PasswordResetRequestType::SEC_USER_PROPERTY => $this->secUserProperty,
        ]);
    }

    public function createFormPasswordReset(): FormInterface
    {
        $action = $this->router->generate(Routes::PASSWORD_RESET_RESET);

        return $this->formFactory->create(PasswordResetType::class, null, [
            self::ACTION             => $action,
            self::METHOD             => self::POST,
            'allow_extra_fields'     => true,
        ]);
    }

    /**
     * @param HasPlainPasswordInterface $user
     */
    public function encodePlainPassword(HasPlainPasswordInterface $user, string $plainPassword = null): string
    {
        $plainPassword ??= $user->getPlainPassword();
        if (null === $plainPassword) {
            throw new PasswordRequired();
        }

        if ( ! $user instanceof UserInterface) {
            throw new UserClassMustImplementUserInterface($user);
        }

        $hasher         = $this->hasherFactory->getPasswordHasher($user);
        $hashedPassword = $hasher->hash($plainPassword);

        if (false === $hasher->verify($hashedPassword, $plainPassword)) {
            throw new PasswordEncodingError();
        }

        return $hashedPassword;
    }

    public function generatePlainPassword(): string
    {
        return ByteString::fromRandom(12)->toString();
    }
}
