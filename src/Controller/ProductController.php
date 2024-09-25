<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{

    private $productRepository;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    /**
    * Affiche la page des produits
    */
    #[Route('/products', name: 'app_product_list')]
    public function showAllProducts(): Response {
        // Récupération des produits en base de données
        $products = $this->productRepository->findAll();
    
        return $this->render('\products\products.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * Affiche la page de détails des produits en fonction de leur ID
     */
    #[Route('/products/{id}', name: 'app_product_details')]
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

    /**
     * Affiche la page du panier 
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/cart', name: 'app_cart')]
    public function showCart(): Response {
        return $this->render('\products\order-cart.html.twig');
    }
}
