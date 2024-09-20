<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
class ProductController extends AbstractController
{

    private $productRepository;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    /**
     * Affiche la page de détails des produits en fonction de leur ID
     */
    #[Route('/products/{id}', name: 'product_details')]
    public function showProductDetails(int $id): Response {
        // Récupération des données du produit en base de données via l'ID
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit n\'existe pas.');
        }

        return $this->render('\products\product-details.html.twig', [
            'product' => $product,
        ]);
    }
}
