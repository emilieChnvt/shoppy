<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $productRepository): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {return $this->redirectToRoute('app_login');}


        return $this->render('product/index.html.twig', [
            'products'=>$productRepository->findAll(),
        ]);
    }
}
