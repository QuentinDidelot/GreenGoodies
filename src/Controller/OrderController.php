<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/order/confirm', name: 'app_order_confirm')]
    public function confirmOrder(EntityManagerInterface $entityManager, ProductRepository $productRepository, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $cart = $session->get('cart', []);
        
        if (empty($cart)) {
            return $this->redirectToRoute('app_cart');
        }

        // Créer une nouvelle commande avec le statut "en attente de paiement"
        $order = new Order();
        $order->setUser($user);
        $order->setOrderDate(new \DateTime());
        $order->setStatus('en attente de paiement'); 
        $totalPrice = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if (!$product) {
                continue;
            }

            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $orderItem->setPrice($product->getPrice());
            $order->addOrderItem($orderItem);

            $totalPrice += $product->getPrice() * $quantity;
        }

        $order->setTotalPrice($totalPrice);

        // Enregistrer la commande en attente
        $entityManager->persist($order);
        $entityManager->flush();

        // Stocker l'ID de la commande dans la session pour utilisation ultérieure
        $session->set('orderId', $order->getId());

        return $this->render('payment/checkout.html.twig', [
            'products' => $productRepository->findBy(['id' => array_keys($cart)]),
            'cart' => $cart,
            'total' => $totalPrice,
        ]);
    }
}
