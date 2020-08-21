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

use SerendipityHQ\Bundle\UsersBundle\Form\Type\PasswordResetRequestType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\PasswordResetType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\UserPasswordChangeType;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Routes;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class PasswordHelper
{
    private const ACTION = 'action';
    private const METHOD = 'method';
    private const POST   = 'POST';

    private string $secUserProperty;
    private FormFactoryInterface $formFactory;
    private RouterInterface $router;
    private UserPasswordEncoderInterface $userPasswordEncoder;

    // @todo Reactivate password generation
    public function __construct(
        string $secUserProperty,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->secUserProperty     = $secUserProperty;
        $this->formFactory         = $formFactory;
        $this->router              = $router;
        $this->userPasswordEncoder = $userPasswordEncoder;
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

    public function encodePlainPassword(UserInterface $user, string $plainPassword): string
    {
        return $this->userPasswordEncoder->encodePassword($user, $plainPassword);
    }
}
