<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee')]
final class EmployeeController extends AbstractController
{
    #[Route('/orders', name: 'app_employee')]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('employee/index.html.twig', [
            'orders'=>$orderRepository->findAll(),
        ]);
    }
}
