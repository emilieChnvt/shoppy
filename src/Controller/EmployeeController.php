<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee')]
final class EmployeeController extends AbstractController
{
    #[Route('/orders', name: 'app_employee')]
    public function index(OrderRepository $orderRepository): Response
    {
        if(!in_array("ROLE_EMPLOYEE", $this->getUser()->getRoles())){ return $this->redirectToRoute('app_login');}
        return $this->render('employee/index.html.twig', [
            'orders'=>$orderRepository->findAll(),
        ]);
    }

    #[Route('/order/{id}', name: 'app_employee_show')]
    public function show(Order $order): Response
    {
        if(!in_array("ROLE_EMPLOYEE", $this->getUser()->getRoles())){ return $this->redirectToRoute('app_login');}

        return $this->render('employee/show.html.twig', [
            'order'=>$order,

        ]);
    }

    #[Route('/order/status/{id}', name: 'app_employee_status')]
    public function statusChanged(Order $order, EntityManagerInterface $entityManager): Response
    {
        if(!in_array("ROLE_EMPLOYEE", $this->getUser()->getRoles())){ return $this->redirectToRoute('app_login');}

        if(!$order){return $this->redirectToRoute('app_employee');}
        if($order->getStatus() === 2 || $order->getStatus() === 3){return $this->redirectToRoute('app_employee_show',['id'=>$order->getId()]);}
        $order->setStatus(2);
        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee');
    }
}
