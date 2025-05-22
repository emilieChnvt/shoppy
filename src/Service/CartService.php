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
        if(!$product){dd('PAS TROUVE');}
        $cart = $this->requestStack->getSession()->get('cart', []); // récupération de cart
       //dd($cart);
        $productId = $product->getId();
        //modification de cart
        if(isset($cart[$productId])){               //modification de cart
            $cart[$productId] = $cart[$productId]+$quantity;         //modification de cart
        }else{
            $cart[$productId] = $quantity;          //modification de cart

        }
        $this->requestStack->getSession()->set('cart', $cart); // $cart set
    }


    public function removeOneUnitFromCart(Product $product): void
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $productId = $product->getId();


        if (isset($cart[$productId])) {
            $cart[$productId]--;

            if ($cart[$productId] === 0) {
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
        //dd($cart);
        foreach ($cart as $item) {
            $total += $item['quantity'] * $item['product']->getPrice();
        }
        return $total;
    }

    public function getCount():int
    {

        $cart = $this->requestStack->getSession()->get("cart", []);
        $count = 0;

        foreach ($cart as $quantity) {
            $count += $quantity;
        }

        return $count;
    }
}