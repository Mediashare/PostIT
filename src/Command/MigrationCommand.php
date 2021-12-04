<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrationCommand extends Command
{
    protected static $defaultName = 'migration';
    protected static $defaultDescription = 'Migrate data for new architecture.';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $posts = $this->em->getRepository(Post::class)->findAll();
        foreach ($posts ?? [] as $post):
            $article = new Article();
            $article->setContent($post->getContent());
            $post->setArticle($article);
            $post->setContent(null);
            $this->em->persist($post);
        endforeach;
        $this->em->flush();

        return Command::SUCCESS;
    }
}
