<?php

namespace App\Controller;

use App\Repository\ProductRepository; // Importer le repository
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Affichage des produits dans la page d'accueil
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Récupération des produits en base de données
        $products = $this->productRepository->findAll();

        return $this->render('home.html.twig', [
            'products' => $products,
        ]);
    }
}
