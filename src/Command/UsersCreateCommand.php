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
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UsersCreateCommand extends Command
{
    protected static $defaultName = 'shq:users:create';
    private EntityManagerInterface $entityManager;
    private UsersManagerRegistry $usersManagerRegistry;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, UsersManagerRegistry $usersManagerRegistry, ValidatorInterface $validator)
    {
        $this->entityManager         = $entityManager;
        $this->usersManagerRegistry  = $usersManagerRegistry;
        $this->validator             = $validator;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Creates a user.')
            ->addArgument('unique', InputArgument::REQUIRED, 'The value to use with the unique field.')
            ->addArgument('pass', InputArgument::REQUIRED, 'The password to assign to the user.')
            ->addOption('provider', 'p', InputOption::VALUE_OPTIONAL)
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
        $io = new SymfonyStyle($input, $output);

        $io->title('Create user');

        $unique   = $input->getArgument('unique');
        $pass     = $input->getArgument('pass');
        $provider = $input->getOption('provider');

        if (null === $unique || null === $pass) {
            return 1;
        }

        $availableManagers = \array_keys($this->usersManagerRegistry->getManagers());
        if (null === $provider) {
            if (1 < \count($availableManagers)) {
                $message = \Safe\sprintf('There is more than one provider configured in your "security.providers". Please, pass the option --provider to the command to use the right one. Available providers are: %s', \implode(', ', $availableManagers));
                $io->error($message);

                return 1;
            }

            $provider = $availableManagers[0];
        }

        if (false === $this->usersManagerRegistry->hasProvider($provider)) {
            $message = \Safe\sprintf('The provider "%s" you passed is not configured in your "security.providers". Available providers are: %s', $provider, \implode(', ', $availableManagers));
            $io->error($message);

            return 1;
        }

        /** @var HasPlainPasswordInterface&UserInterface $user */
        $user   = $this->create($provider, $unique, $pass);
        $errors = $this->validator->validate($user);

        if ((\is_countable($errors) ? \count($errors) : 0) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $io->writeln(\Safe\sprintf('<error>%s (%s => %s)</error>', $error->getMessage(), $error->getPropertyPath(), $error->getInvalidValue()));
            }
            $message = \Safe\sprintf('Impossible to create the user "%s".', $unique);

            $io->error($message);

            return 1;
        }

        $this->entityManager->flush();

        $io->writeln(\Safe\sprintf('Password for user %s: %s', $unique, $user->getPlainPassword()));
        $message = \Safe\sprintf('User %s created.', $unique);
        $io->success($message);

        return 0;
    }

    protected function create(string $provider, string $unique, string $pass): UserInterface
    {
        /** @var UsersManagerInterface $manager */
        $manager = $this->usersManagerRegistry->getManager($provider)->create($unique, $pass);

        return $manager->create($unique, $pass);
    }
}
