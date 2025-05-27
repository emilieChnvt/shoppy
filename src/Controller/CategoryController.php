<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryForm;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class CategoryController extends AbstractController
{
    public function index(CategoryRepository $repo): Response
    {
        $categories = $repo->findAll();

        return $this->render('product/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/show/{id}', name: 'app_category_show')]
    public function showCategory(Category $category, CategoryRepository $categoryRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'category' => $category,
            'products' => $category->getProduct(),
            'categories'=> $categoryRepository->findAll(),
        ]);
    }


    #[Route('/category/create', name: 'app_category_create')]
    public function create(Request $request, EntityManagerInterface $manager, CategoryRepository $categoryRepository): Response
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
            return $this->redirectToRoute('app_category_create');
        }

        return $this->render('category/create.html.twig', [
            'categoryForm' => $categoryForm->createView(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete')]
    public function delete(Category $category, EntityManagerInterface $manager): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_login');
        }
        if($category){
            $manager->remove($category);
            $manager->flush();
        }
        return $this->redirectToRoute('app_category_create');


    }




}
