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

use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\SHQUsersBundle;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

final class UserPasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                self::LABEL_KEY              => 'serendipity_hq.user.form.label.password_change.old_password.label',
                self::MAPPED_KEY             => false,
                self::TRANSLATION_DOMAIN_KEY => SHQUsersBundle::TRANSLATION_DOMAIN,
                self::CONSTRAINTS_KEY        => [new UserPassword([self::MESSAGE_KEY => 'serendipity_hq.user.form.error.old_password.passwords_mismatch'])],
            ])
            ->add(HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD, ConfirmedPasswordType::class);
    }
}
