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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Controller;

use SerendipityHQ\Bundle\UsersBundle\Form\Type\ConfirmedPasswordType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\PasswordResetRequestType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\PasswordResetType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\UserPasswordChangeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final class TestController extends AbstractController
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage, private readonly FormFactoryInterface $formFactory, private readonly Environment $twig)
    {
    }

    public function passwordChange(Request $request): Response
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (null === $user) {
            throw $this->createAccessDeniedException('User must be logged in.');
        }

        $form = $this->formFactory->create(UserPasswordChangeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response('Form is valid');
        }

        if ($form->isSubmitted() && false === $form->isValid()) {
            return new Response($this->twig->render('test/password_change.html.twig', [
                'form' => $form->createView(),
            ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new Response($this->twig->render('test/password_change.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    public function confirmedPassword(Request $request): Response
    {
        $form = $this->formFactory->create(ConfirmedPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response('Form is valid');
        }

        if ($form->isSubmitted() && false === $form->isValid()) {
            return new Response($this->twig->render('test/confirmed_password.html.twig', [
                'form' => $form->createView(),
            ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new Response($this->twig->render('test/confirmed_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    public function passwordResetRequest(Request $request): Response
    {
        $form = $this->formFactory->create(PasswordResetRequestType::class, null, [
            PasswordResetRequestType::SEC_USER_PROPERTY => 'email',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response('Form is valid');
        }

        if ($form->isSubmitted() && false === $form->isValid()) {
            return new Response($this->twig->render('test/password_reset_request.html.twig', [
                'form' => $form->createView(),
            ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new Response($this->twig->render('test/password_reset_request.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    public function passwordReset(Request $request): Response
    {
        $form = $this->formFactory->create(PasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return new Response('Form is valid');
        }

        if ($form->isSubmitted() && false === $form->isValid()) {
            return new Response($this->twig->render('test/password_reset.html.twig', [
                'form' => $form->createView(),
            ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new Response($this->twig->render('test/password_reset.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
