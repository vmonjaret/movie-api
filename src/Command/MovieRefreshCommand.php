<?php

namespace App\Command;

use App\Entity\Movie;
use App\Manager\MovieManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tmdb\ApiToken;
use Tmdb\Client;
use Tmdb\Repository\MovieRepository;

class MovieRefreshCommand extends Command
{
    protected static $defaultName = 'movie:refresh';
    private $entityManager;
    private $movieManager;

    public function __construct(EntityManagerInterface $entityManager, MovieManager $movieManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->movieManager= $movieManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $token = new ApiToken(getenv('TMDBD_API_KEY'));
        $client = new Client($token);
        $movieRepository = new MovieRepository($client);

        $movies = $this->entityManager->getRepository(Movie::class)->findAll();

        foreach ($movies as $movie) {
            /**
             * @var \Tmdb\Model\Movie $api
             */
            $api = $movieRepository->load($movie->getId());
            $this->movieManager->updateMovie($movie, $api);
        }

        $this->entityManager->flush();

        $io->success('Movies refreshed');
    }
}
