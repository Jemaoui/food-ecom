<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:create-user-admin',
    description: 'Create a new admin user',
)]
class CreateUserAdminCommand extends Command
{
    protected static $defaultName = 'app:create-user-admin';
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new admin user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the admin user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the admin user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin user successfully created.');

        return Command::SUCCESS;
    }
}
