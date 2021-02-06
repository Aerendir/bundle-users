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
use Symfony\Component\Form\FormBuilderInterface;

final class PasswordResetType extends AbstractType
{
    /**
     * This is probably a bug of Phan (#4155).
     *
     * @suppress PhanUnusedPublicFinalMethodParameter
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD, ConfirmedPasswordType::class);
    }
}
