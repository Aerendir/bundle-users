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

namespace SerendipityHQ\Bundle\UsersBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use function Safe\sprintf;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserCreateCommand extends AbstractUsersCommand
{
    protected static $defaultName  = 'shq:user:create';
    protected static string $title = 'Create user';
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, UsersManagerRegistry $usersManagerRegistry, ValidatorInterface $validator)
    {
        $this->validator             = $validator;
        parent::__construct($entityManager, $usersManagerRegistry);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('Creates a user.')
            ->addArgument('pass', InputArgument::REQUIRED, 'The password to assign to the user.')
            ->setHelp(
<<<'EOT'
The <info>%command.name%</info> command creates a user:

  <info>php %command.full_name% Aerendir P4sSw0rD</info>

The second parameter is the password.

The first parameter is the value that your entity expects as the unique one.

So, if your application uses email to identify users, then you will launch the command this way:

  <info>php %command.full_name% aerendir@serendipityhq.com P4sSw0rD</info>

If your application, instead, uses the username to identify the users, then you will launch the command this way;

  <info>php %command.full_name% Aerendir P4sSw0rD</info>

EOT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $initialized = parent::execute($input, $output);
        if (0 !== $initialized) {
            return (int) $initialized;
        }

        $pass = $input->getArgument('pass');
        if (false === \is_string($pass)) {
            return 1;
        }

        /** @var HasPlainPasswordInterface&UserInterface $user */
        $user   = $this->create($pass);
        $errors = $this->validator->validate($user);

        if ((\is_countable($errors) ? \count($errors) : 0) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $this->io->writeln(sprintf('<error>%s (%s => %s)</error>', $error->getMessage(), $error->getPropertyPath(), $error->getInvalidValue()));
            }
            $message = sprintf('Impossible to create the user "%s".', $this->unique);

            $this->io->error($message);

            return 1;
        }

        $this->entityManager->flush();

        $this->io->writeln(sprintf('Password for user %s: %s', $this->unique, $user->getPlainPassword()));
        $message = sprintf('User %s created.', $this->unique);
        $this->io->success($message);

        return 0;
    }

    protected function create(string $pass): UserInterface
    {
        $manager = $this->usersManagerRegistry->getManager($this->provider);

        return $manager->create($this->unique, $pass);
    }
}
