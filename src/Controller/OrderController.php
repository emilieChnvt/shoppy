<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\AddressForm;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order/billingaddress', name: 'app_order_billingaddress')]
    public function billingAddress(Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressForm::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setProfile($this->getUser()->getProfile());
            $entityManager->persist($address);
            $entityManager->flush();

            // Redirige vers shipping address avec id billing
            return $this->redirectToRoute('app_order_shippingaddress', [
                'id' => $address->getId()
            ]);
        }

        return $this->render('order/billingaddress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/order/shippingaddress/{id}', name: 'app_order_shippingaddress')]
    public function shippingAddress(Request $request, EntityManagerInterface $entityManager, AddressRepository $addressRepository, int $id): Response
    {
        $billingAddress = $addressRepository->find($id);
        if (!$billingAddress) {
            throw $this->createNotFoundException('Billing address not found');
        }

        $address = new Address();
        $form = $this->createForm(AddressForm::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setProfile($this->getUser()->getProfile());
            $entityManager->persist($address);
            $entityManager->flush();


            return $this->redirectToRoute('app_order_payment', [
                'idBilling' => $billingAddress->getId(),
                'idShipping' => $address->getId(),
            ]);
        }

        return $this->render('order/shippingaddress.html.twig', [
            'billingAddress' => $billingAddress,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/order/payment/{idBilling}/{idShipping}', name: 'app_order_payment')] //recap commande + payer
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


    // user paye puis redirigé vers validate

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


            //new stock after an order
            $product = $cartItem['product'];
            $newtStock = $product->getStock() - $cartItem['quantity'];
            $product->setStock($newtStock);
            $manager->persist($product);
        }
        $manager->flush();
        $cartService->emptyCart();
        return $this->redirectToRoute('app_my_orders');


    }

    #[Route('/myorders', name: 'app_my_orders')]
    public function myOrders(OrderRepository $orderRepository): Response
    {
        $profile = $this->getUser()->getProfile();

        $orders = $orderRepository->findBy(['customer' => $profile]);

        return $this->render('order/my_orders.html.twig', [
            'orders' => $orders,
        ]);
    }
}
