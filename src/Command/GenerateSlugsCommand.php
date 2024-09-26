<?php

namespace App\Command;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class GenerateSlugsCommand extends Command
{
    protected static $defaultName = 'app:generate-slugs';

    private $productRepository;
    private $entityManager;
    private $slugger;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $products = $this->productRepository->findAll();

        foreach ($products as $product) {
            if (empty($product->getSlug())) {
                // Générer le slug s'il est vide
                $slug = $this->slugger->slug($product->getName())->lower();
                $product->setSlug($slug);
                $this->entityManager->persist($product);
                $output->writeln("Slug généré pour le produit ID: {$product->getId()}, Nom: {$product->getName()}");
            }
        }

        $this->entityManager->flush();
        $output->writeln("Slugs générés avec succès.");

        return Command::SUCCESS;
    }
}