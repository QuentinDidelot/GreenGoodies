<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/payment/checkout', name: 'app_order_payment')]
    public function checkout(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            return $this->redirectToRoute('app_cart');
        }

        $productsForStripe = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);

            if (!$product) {
                continue;
            }

            $productsForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getName(),
                    ],
                    'unit_amount' => $product->getPrice() * 100, 
                ],
                'quantity' => $quantity,
            ];

            $total += $product->getPrice() * $quantity;
        }

        Stripe::setApiKey('sk_test_51LiGzfIKG6NL7lD76dkjsaykpkzl5VnRW5UzH3r9PppxLgOmOnw6RKAUELQDxtL1hD1usdDSwa3KQvdETq69uTDn0096MNLLri');

        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [$productsForStripe],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_order_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_cart', [], UrlGeneratorInterface::ABSOLUTE_URL),

        ]);

        return $this->redirect($checkoutSession->url);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/order/success', name: 'app_order_success')]
    public function success(SessionInterface $session, EntityManagerInterface $entityManager, OrderRepository $orderRepository): Response
    {
        // Récupérer l'ID de la commande en session
        $orderId = $session->get('orderId');
        if (!$orderId) {
            return $this->redirectToRoute('app_cart');
        }

        // Récupérer la commande
        $order = $orderRepository->find($orderId);
        if (!$order) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        // Mise à jour du statut de la commande (par exemple "payée")
        $order->setStatus('paid');
        $entityManager->flush();

        // Suppression de l'ID de commande en session
        $session->remove('orderId');

        // Affiche une page de succès après le paiement
        return $this->render('order/success.html.twig', [
            'message' => 'Votre paiement a été effectué avec succès !',
        ]);
    }
}
