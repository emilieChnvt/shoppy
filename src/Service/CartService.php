<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository,
    ){}


    public function getCart(): array
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $objectcart = [];

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepository->find($productId);
            if ($product) {
                $objectcart[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        return $objectcart;
    }




    public function addToCart(Product $product, int $quantity):void
    {
        $cart = $this->requestStack->getSession()->get('cart', []); // récupération de cart
        $productId = $product->getId();             //modification de cart
        if(isset($cart[$productId])){               //modification de cart
            $cart[$productId] += $quantity;         //modification de cart
        }else{
            $cart[$productId] = $quantity;          //modification de cart

        }
        $this->requestStack->getSession()->set('cart', $cart); // $cart set
    }


    public function removeFromCart(Product $product, int $quantity):void
    {
        $cart = $this->requestStack->getSession()->get('cart', []); // récupération de cart
        $productId = $product->getId();             //modification de cart
        if(isset($cart[$productId])){               //modification de cart
            $cart[$productId] -= $quantity;         //modification de cart
        }
        if($cart[$productId] <= 0){
            unset($cart[$productId]);
        }
        $this->requestStack->getSession()->set('cart', $cart); // $cart set
    }

    public function emptyCart(){
        $this->requestStack->getSession()->remove("cart");
    }
    public function getTotalPrice():float
    {
        $cart = $this->getCart();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['quantity'] * $item['product']->getPrice();
        }
        return $total;
    }
}