<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Images;
use App\Entity\Product;
use App\Form\ImagesForm;
use App\Form\ProductAutocompleteField;
use App\Form\ProductForm;
use App\Form\ProductSearchForm;
use App\Repository\CategoryRepository;
use App\Repository\ImagesRepository;
use App\Repository\ProductRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Product $product, CategoryRepository $categoryRepository): Response
    {

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'categories' => $categoryRepository->findAll(),

        ]);
    }

    #[Route('/products/search', name: 'search_product')]
    public function search(Request $request ): Response
    {
        $form = $this->createForm(ProductSearchForm::class );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->get('product')->getData();

            if (!$product) {
                return $this->redirectToRoute('search_product');
            }

            return $this->redirectToRoute('app_product_show', ['id'=>$product->getId()]);
        }
        return $this->render('search/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        if(!$this->getUser()) {return $this->redirectToRoute('app_login');}

        $form = $this->createForm(ProductSearchForm::class);
        $form->handleRequest($request);
        return $this->render('product/index.html.twig', [
            'products'=>$productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'form' => $form->createView(),

        ]);
    }




}
