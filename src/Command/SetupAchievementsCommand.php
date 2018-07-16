<?php

namespace App\Command;

use App\Entity\Achievement;
use App\Repository\AchievementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetupAchievementsCommand extends Command
{
    protected static $defaultName = 'setup:achievements';

    private $entityManager;
    private $achievementRepository;

    public function __construct(?string $name = null, EntityManagerInterface $entityManager, AchievementRepository $achievementRepository)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->achievementRepository = $achievementRepository;
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

        $achievements = array(
            array('name' => 'Stalker', 'description' => 'Consulter un autre profil', 'type' => Achievement::STALKER),
            array('name' => 'Auteur', 'description' => 'Ecrire un commentaire sur un film', 'type' => Achievement::AUTHOR),
            array('name' => 'Ecrivain', 'description' => 'Commentaire de plus de 100 caractères', 'type' => Achievement::WRITER),
        );

        foreach ($achievements as $achievement) {
            $badge = $this->achievementRepository->findOneBy(array('type' => $achievement['type']));
            if (null === $badge) {
                $achievement = (new Achievement())->createFromArray($achievement);
                $this->entityManager->persist($achievement);
            }
        }

        $this->entityManager->flush();

        $io->success('Successfully imported achievements.');
    }
}
