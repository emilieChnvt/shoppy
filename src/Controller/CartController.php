<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cartService, CategoryRepository $categoryRepository): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart'=>$cartService->getCart(),
            'total'=>$cartService->getTotalPrice(),
           'categories'=>$categoryRepository->findAll(),
           // 'count'=>$cartService->getCount(),

        ]);
    }


    #[Route('/cart/add/{id}/', name: 'app_cart_add')]
    public function add(CartService $cartService, Product $product, Request $request): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);

        if(!$this->getUser()){return $this->redirectToRoute('app_login');}
        $cartService->addToCart($product, $quantity);
        return $this->redirectToRoute('app_cart');

    }


    #[Route('/cart/remove/{id}/{quantity}', name: 'app_cart_remove')]
    public function removeOneFromCart(CartService $cartService, Product $product, int $quantity): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute('app_login');}
        $cartService->removeOneUnitFromCart($product, $quantity);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/empty', name: 'app_cart_empty')]
    public function removeAllFromCart(CartService $cartService): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute('app_login');}
        $cartService->emptyCart();
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/removeProduct/{id}', name: 'app_cart_removeoneproductfromcart')]
    public function removeOneProductFromCart(CartService $cartService, Product $product): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute('app_login');}
        if(!$product){return $this->redirectToRoute('app_products');}
        $cartService->removeProductFromCart($product);
        return $this->redirectToRoute('app_cart');
    }
}
