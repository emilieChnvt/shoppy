<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart'=>$cartService->getCart(),

        ]);
    }


    #[Route('/cart/add/{id}/{quantity}', name: 'app_cart_add')]
    public function add(CartService $cartService, Product $product, int $quantity): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute('app_login');}
        if(!$product){return $this->redirectToRoute('app_products');}
        $cartService->addToCart($product, $quantity);
        return $this->redirectToRoute('app_cart');


    }


    #[Route('/cart/remove/{id}/{quantity}', name: 'app_cart_remove')]
    public function removeOneFromCart(CartService $cartService, Product $product, int $quantity): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute('app_login');}
        if(!$product){return $this->redirectToRoute('app_products');}
        $cartService->removeFromCart($product, $quantity);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/empty', name: 'app_cart_empty')]
    public function removeAllFromCart(CartService $cartService): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute('app_login');}
        $cartService->emptyCart();
        return $this->redirectToRoute('app_cart');
    }
}
