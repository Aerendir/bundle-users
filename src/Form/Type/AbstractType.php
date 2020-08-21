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

use Symfony\Component\Form\AbstractType as BaseAbstractType;

abstract class AbstractType extends BaseAbstractType
{
    protected const CONSTRAINTS_KEY = 'constraints';

    protected const LABEL_KEY = 'label';

    protected const MAPPED_KEY = 'mapped';

    protected const MESSAGE_KEY = 'message';

    protected const TRANSLATION_DOMAIN_KEY = 'translation_domain';
}
