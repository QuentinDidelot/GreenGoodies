<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

        $products = $this->productRepository->findAll();
    
        return $this->render('\products\products.html.twig', [
            'products' => $products,
        ]);
    }

/**
 * Affiche la page de détails des produits en fonction de leur ID
 */
#[Route('/products/{id}', name: 'app_product_details')]
public function showProductDetails(int $id, SessionInterface $session): Response {
    $product = $this->productRepository->find($id);

    if (!$product) {
        throw $this->createNotFoundException('Le produit n\'existe pas.');
    }

    // Récupérez le panier de l'utilisateur
    $cart = $session->get('cart', []);
    // Obtenez la quantité actuelle du produit dans le panier
    $currentQuantity = $cart[$product->getId()] ?? 0; // Défaut à 0 si le produit n'est pas dans le panier

    return $this->render('\products\product-details.html.twig', [
        'product' => $product,
        'currentQuantity' => $currentQuantity, // Passez la quantité actuelle au template
    ]);
}



    /**
     * Affiche la page du panier 
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/cart', name: 'app_cart')]
    public function showCart(SessionInterface $session): Response {
        // Récupérez le panier de l'utilisateur
        $cart = $session->get('cart', []);
        
        // Récupérez les produits correspondants
        $products = $this->productRepository->findBy(['id' => array_keys($cart)]);
        
        $total = 0;
        foreach ($products as $product) {
            $total += $product->getPrice() * $cart[$product->getId()]; // Multipliez le prix par la quantité
        }
    
        return $this->render('\products\order-cart.html.twig', [
            'cart' => $cart,
            'products' => $products,
            'total' => $total,
        ]);
    }
    
    

    /**
     * Ajout des produits au panier si l'utilisateur est connecté
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function addToCart(Product $product, SessionInterface $session, Request $request): Response
    {
        // Récupérez le panier de l'utilisateur
        $cart = $session->get('cart', []);
    
        // Récupérez la quantité depuis la requête
        $quantity = $request->request->get('quantity', 1);
        
        // Vérifiez si la quantité est supérieure à 0
        if ($quantity > 0) {
            // Si le produit est déjà dans le panier, mettez à jour la quantité
            $cart[$product->getId()] = $quantity;
        } else {
            // Sinon, retirez le produit du panier
            unset($cart[$product->getId()]);
        }
    
        // Enregistrez le panier mis à jour
        $session->set('cart', $cart);
    
        // Rediriger vers la page de détails du produit
        return $this->redirectToRoute('app_product_details', ['id' => $product->getId()]);
    }
    
    

    /**
     * Vide le panier
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(SessionInterface $session): Response {

        // Videz le panier de l'utilisateur
        $session->remove('cart');

        return $this->redirectToRoute('app_home');
    }
}
