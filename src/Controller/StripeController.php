<?php
namespace App\Controller;

use App\Repository\AddressRepository;
use App\Service\CartService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    #[Route('/checkout/{idBilling}/{idShipping}', name: 'stripe_checkout')]
    public function checkout(Request $request, CartService $cartService, AddressRepository $addressRepository, $idBilling,$idShipping): JsonResponse
    {
        $billing = $addressRepository->find($idBilling);
        $shipping = $addressRepository->find($idShipping);
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);



        $cart = $cartService->getCart();
        $lineItems = [];
        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['product']->getName(),
                    ],
                    'unit_amount' => $item['product']->getPrice() * 100, // centimes
                ],
                'quantity' => $item['quantity'],
            ];
        }
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_order_validate', [
                'idBilling' => $idBilling,
                'idShipping' => $idShipping
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'locale' => 'fr',
            // Pas besoin de billing_address_collection ici
        ]);



        return new JsonResponse(['id' => $session->id]);
    }

    #[Route('/success', name: 'stripe_success')]
    public function success(): Response
    {
        return new Response('<h2>Paiement réussi !</h2>');
    }

    #[Route('/cancel', name: 'stripe_cancel')]
    public function cancel(): Response
    {
        return new Response('<h2>Paiement annulé.</h2>');
    }


    #[Route('/paiement', name: 'checkout_page')]
    public function paiement(CartService $cartService): Response
    {
        return $this->render('order/checkout.html.twig', [
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
            'total' => $cartService->getTotalPrice(),
            'cart' => $cartService->getCart(),
        ]);
    }


}