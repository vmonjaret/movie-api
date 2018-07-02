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
use Tmdb\Model\Movie as TmdbMovie;
use Tmdb\Repository\MovieRepository;

class MovieImportCommand extends Command
{
    protected static $defaultName = 'movie:import';

    private $entityManager;
    private $movieManager;

    /**
     * MovieImportCommand constructor.
     * @param $entityManager
     * @param $movieRepository
     */
    public function __construct(EntityManagerInterface $entityManager, MovieManager $movieManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->movieManager = $movieManager;
    }


    protected function configure()
    {
        $this
            ->setDescription('Import popular movies')
            ->addArgument('loop', InputArgument::OPTIONAL, 'Number of iterations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $loop = $input->getArgument('loop') ?? 10;

        $token = new ApiToken(getenv('TMDBD_API_KEY'));
        $client = new Client($token);
        $movieRepository = new MovieRepository($client);

        for ($i = 1; $i <= $loop; $i++) {
            $movies = $movieRepository->getPopular(array('page' => $i, 'language' => 'fr'));
            foreach ($movies as $movie) {
                $this->movieManager->getMovie($movie);
            }
            $this->entityManager->flush();
            $io->note("Loop $i / $loop");
        }

        $io->success("Import complete.");
    }
}
