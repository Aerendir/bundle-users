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

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PasswordResetRequestType extends AbstractType
{
    public const SEC_USER_PROPERTY = 'sec_user_property';

    /**
     * @psalm-suppress MixedArgument
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add($options[self::SEC_USER_PROPERTY], TextType::class, [
                self::CONSTRAINTS_KEY => [
                    new NotBlank([
                        self::MESSAGE_KEY => 'serendipity_hq.user.form.error.primary.not_blank',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(self::SEC_USER_PROPERTY);
        $resolver->setAllowedTypes(self::SEC_USER_PROPERTY, 'string');
    }
}
