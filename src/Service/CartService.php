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


    public function getCart():array
    {
        $cart = $this->requestStack->getSession()->get('cart', [] );
        $objectCart = [];
        foreach ($cart as $productId => $quantity){
            $item = [
                "product"=> $this->productRepository->find($productId),
                "quantity" => $quantity,
            ];
            $objectCart[] = $item;
        }
        return $objectCart;
    }


    public function addToCart(Product $product, int $quantity):void
    {
        $cart = $this->requestStack->getSession()->get('cart', []); // récupération de cart
        $productId = $product->getId();
        //modification de cart
        if(isset($cart[$productId])){               //modification de cart
            $cart[$productId] += $quantity;         //modification de cart
        }else{
            $cart[$productId] = $quantity;          //modification de cart

        }
        $this->requestStack->getSession()->set('cart', $cart); // $cart set
    }


    public function removeOneUnitFromCart(Product $product, int $quantity = 1): void
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $productId = $product->getId();


        if (isset($cart[$productId])) {
            $cart[$productId] -= $quantity;

            if ($cart[$productId] <= 0) {
                unset($cart[$productId]);
            }
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function removeProductFromCart(Product $product):void
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $productId = $product->getId();
        if(isset($cart[$productId])){
            unset($cart[$productId]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
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

    public function getCount():int
    {
        $cart = $this->getCart();
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}