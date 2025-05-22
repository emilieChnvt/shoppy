<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order/billingaddress', name: 'app_order_billingaddress')]
    public function billingAddress(): Response
    {

        return $this->render('order/billingaddress.html.twig');
    }
    #[Route('/order/shippingaddress/{id}', name: 'app_order_shippingaddress')]
    public function shippingAddress(Address $address): Response
    {

        return $this->render('order/shippingaddress.html.twig',[
            'billingAddress' => $address
        ]);
    }


    #[Route('/order/payment/{idBilling}/{idShipping}', name: 'app_order_payment')]
    public function payment(CartService $cartService,AddressRepository $addressRepository, $idBilling, $idShipping): Response
    {
        $billing = $addressRepository->find($idBilling);
        $shipping = $addressRepository->find($idShipping);


        return $this->render('order/payement.html.twig',[
            'cart' => $cartService->getCart(),
            'total'=>$cartService->getTotalPrice(),
            'billing' => $billing,
            'shipping' => $shipping,
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],      ]);
    }


    #[Route('/order/validate/{idBilling}/{idShipping}', name: 'app_order_validate')]

    public function validate(EntityManagerInterface $manager,  AddressRepository $addressRepository, $idBilling, $idShipping, CartService $cartService):Response
    {
        $billing = $addressRepository->find($idBilling);
        $shipping = $addressRepository->find($idShipping);

        $order = new Order();
        $order->setBillingAdress($billing);
        $order->setShippingAddress($shipping);
        $order->setCreateAt(new \DateTimeImmutable());
        $order->setCustomer($this->getUser()->getProfile());
        $order->setTotal($cartService->getTotalPrice());
        $order->setStatus(1);
        $manager->persist($order);

        foreach($cartService->getCart() as $cartItem){
            $orderItem = new OrderItem();
            $orderItem->setOfOrder($order);
            $orderItem->setProduct($cartItem['product']);
            $orderItem->setQuantity($cartItem['quantity']);
            $manager->persist($orderItem);
        }
        $manager->flush();
        $cartService->emptyCart();
        return $this->redirectToRoute('app_cart');


    }

    #[Route('/myorders', name: 'app_my_orders')]
    public function myOrders(): Response
    {
        return $this->render('order/my_orders.html.twig',[]);
    }
}
