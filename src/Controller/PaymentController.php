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

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

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
        $orderId = $session->get('orderId');
        if (!$orderId) {
            return $this->redirectToRoute('app_cart');
        }
    
        $order = $orderRepository->find($orderId);
        if (!$order) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        $session->remove('cart');
        $session->remove('orderId');
    
        return $this->render('payment/success.html.twig', [
            'message' => 'Votre paiement a été effectué avec succès !',
        ]);
    }
    
}
