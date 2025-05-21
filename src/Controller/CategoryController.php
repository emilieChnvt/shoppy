<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/category/create', name: 'app_category_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }
        $category = new Category();
        $categoryForm = $this->createForm(CategoryForm::class , $category);
        $categoryForm->handleRequest($request);
        if($categoryForm->isSubmitted() && $categoryForm->isValid()){
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('app_product_addImages');
        }

        return $this->render('category/create.html.twig', [
            'categoryForm' => $categoryForm->createView(),
        ]);
    }
}
