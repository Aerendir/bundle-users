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

namespace SerendipityHQ\Bundle\UsersBundle\Form\Type;

use SerendipityHQ\Bundle\UsersBundle\SHQUsersBundle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;

final class UserPasswordChangeType extends AbstractType
{
    /** @var string */
    protected const CONSTRAINTS_KEY = 'constraints';

    /** @var string */
    protected const LABEL_KEY = 'label';

    /** @var string */
    protected const MAPPED_KEY = 'mapped';

    /** @var string */
    protected const TRANSLATION_DOMAIN_KEY = 'translation_domain';

    // @todo Make this configurable. Max length allowed by Symfony for security reasons.
    /**
     * @var int
     */
    private const CONSTRAINT_PASSWORD_LENGTH_MAX = 4096;

    // @todo Make this configurable
    /**
     * @var int
     */
    private const CONSTRAINT_PASSWORD_LENGTH_MIN = 6;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                self::LABEL_KEY              => 'serendipity_hq.user.form.password_change.old_password.label',
                self::MAPPED_KEY             => false,
                self::TRANSLATION_DOMAIN_KEY => SHQUsersBundle::TRANSLATION_DOMAIN,
                self::CONSTRAINTS_KEY        => [new UserPassword(['message' => 'serendipity_hq.user.form.password_change.wrong_password.error'])],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'                       => PasswordType::class,
                'invalid_message'            => 'serendipity_hq.user.form.password_change.new_password.error.mismatch',
                self::TRANSLATION_DOMAIN_KEY => SHQUsersBundle::TRANSLATION_DOMAIN,
                'first_options'              => [
                    self::LABEL_KEY       => 'serendipity_hq.user.form.password_change.new_password.label',
                    self::CONSTRAINTS_KEY => [
                        new Length([
                           'min' => self::CONSTRAINT_PASSWORD_LENGTH_MIN,
                           // 'Your password should be at least {{ limit }} characters'
                           'minMessage' => 'serendipity_hq.user.form.password_change.new_password.error.too_long',
                           'max'        => self::CONSTRAINT_PASSWORD_LENGTH_MAX,
                       ]),
                    ],
                ],
                'second_options' => [self::LABEL_KEY => 'serendipity_hq.user.form.password_change.confirm_new_password.label'],
            ]);
    }
}
