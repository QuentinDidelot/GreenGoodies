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
#[Route('/products/{id}-{slug}', name: 'app_product_details', requirements: ['id' => '\d+'])]
public function showProductDetails(int $id, string $slug, SessionInterface $session): Response
{
    $product = $this->productRepository->find($id);

    if (!$product) {
        throw $this->createNotFoundException('Le produit n\'existe pas.');
    }

    // Si le slug de l'URL ne correspond pas au slug du produit, rediriger vers l'URL correcte
    if ($product->getSlug() !== $slug) {
        return $this->redirectToRoute('app_product_details', [
            'id' => $product->getId(),
            'slug' => $product->getSlug(),
        ], 301);
    }

    $cart = $session->get('cart', []);

    $currentQuantity = $cart[$product->getId()] ?? 0;

    return $this->render('\products\product-details.html.twig', [
        'product' => $product,
        'currentQuantity' => $currentQuantity,
    ]);
}



    /**
     * Affiche la page du panier 
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/cart', name: 'app_cart')]
    public function showCart(SessionInterface $session): Response {

        // Pour récupérer le panier de l'utilisateur
        $cart = $session->get('cart', []);
        
        // Pour récupérer les produits correspondants
        $products = $this->productRepository->findBy(['id' => array_keys($cart)]);
        
        $total = 0;
        foreach ($products as $product) {
            $total += $product->getPrice() * $cart[$product->getId()];
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
        // Pour récupérer le panier de l'utilisateur
        $cart = $session->get('cart', []);

        // Pour récupérer la quantité depuis la requête
        $quantity = $request->request->get('quantity', 1);

        if ($quantity > 0) {
            // Si le produit est déjà dans le panier, mettre à jour la quantité
            $cart[$product->getId()] = $quantity;
        } else {
            unset($cart[$product->getId()]);
        }

        // MàJ du panier
        $session->set('cart', $cart);

        $this->addFlash(
            'success', 
            sprintf('L\'article "%s" (x%d) a bien été ajouté au panier.', $product->getName(), $quantity)
        );
        

        // Redirection avec l'ID et le slug
        return $this->redirectToRoute('app_product_details', [
            'id' => $product->getId(),
            'slug' => $product->getSlug(),
        ]);
    }

    
    

    /**
     * Vide le panier
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(SessionInterface $session): Response {

        $session->remove('cart');

        $this->addFlash("success", "Votre panier a bien été vidé !");

        return $this->redirectToRoute('app_home');
    }
}
