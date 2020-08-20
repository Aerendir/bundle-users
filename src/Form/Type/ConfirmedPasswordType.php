<?php

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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * This form type is used to create a new password after a reset request.
 */
final class ConfirmedPasswordType extends AbstractType
{
    /**
     * Max length allowed by Symfony for security reasons.
     *
     * @var int
     */
    private const CONSTRAINT_PASSWORD_LENGTH_MAX = 4096;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'type'                       => PasswordType::class,
           'invalid_message'            => 'serendipity_hq.user.form.password_change.new_password.error.mismatch',
           self::TRANSLATION_DOMAIN_KEY => SHQUsersBundle::TRANSLATION_DOMAIN,
           'first_options'              => [
               self::LABEL_KEY       => 'serendipity_hq.user.form.password_change.new_password.label',
               self::CONSTRAINTS_KEY => [
                   new NotBlank([self::MESSAGE_KEY => 'serendipity_hq.user.form.error.confirmed_password.not_blank']),
                   new Length([
                                  // 'Your password should be at least {{ limit }} characters'
                                  'maxMessage' => 'serendipity_hq.user.form.error.confirmed_password.too_long',
                                  'max'        => self::CONSTRAINT_PASSWORD_LENGTH_MAX,
                              ]),
               ],
           ],
           'second_options' => [self::LABEL_KEY => 'serendipity_hq.user.form.label.confirmed_password.confirm_password'],
       ]);
    }

    public function getParent(): string
    {
        return RepeatedType::class;
    }
}
