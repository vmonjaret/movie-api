<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SetupAdminCommand extends Command
{
    protected static $defaultName = 'setup:admin';
    private $passwordEncoder;
    private $entityManager;

    public function __construct(?string $name = null, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);

        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager= $entityManager;
    }


    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
        $helper = $this->getHelper('question');

        $username = new Question('Please enter the username: ');
        $username = $helper->ask($input, $output, $username);

        $email = new Question('Please enter the email: ');
        $email = $helper->ask($input, $output, $email);

        $password = new Question('Please enter the password: ');
        $password->setHidden(true);
        $password->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $password);


        $user = new User();
        $user->setUsername($username)
            ->setEmail($email)
            ->setRoles(array("ROLE_USER", "ROLE_ADMIN"))
        ;
        $encoded = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encoded);

        $io->note($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Successfully added admin');
    }
}
