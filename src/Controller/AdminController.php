<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Product;
use App\Form\ImagesForm;
use App\Form\ProductForm;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/products', name: 'app_admin_products')]
    public function index(ProductRepository $productRepository): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {return $this->redirectToRoute('app_login');}


        return $this->render('admin/products.html.twig', [
            'products'=>$productRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}', name: 'app_admin_product')]
    public function show(Product $product): Response
    {
        return $this->render('admin/show.html.twig', [
            'product'=>$product,
        ]);
    }

    #[Route('/products/{id}/add-images', name: 'app_product_addImages_product')]
    public function addImages(Request $request, EntityManagerInterface $entityManager, Product $product): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ImagesForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('imageFiles')->getData();

            foreach ($files as $file) {
                $image = new Images();
                $image->setImageFile($file);
                $image->setProduct($product);
                $entityManager->persist($image);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        return $this->render('admin/add_image.html.twig', [
            'imageForm' => $form->createView(),
            'product' => $product,
            'images' => $product->getImages()
        ]);
    }


    #[Route('/product/create', name: 'app_product_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_login');
        }

        $product = new Product();
        $productForm = $this->createForm(ProductForm::class, $product);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $product->setCreateAt(new \DateTimeImmutable());
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_addImages_product', ['id' => $product->getId()]);
        }

        return $this->render('admin/create.html.twig', [
            'productForm' => $productForm,
        ]);
    }

    #[Route('/product/edit/{id}', name: 'app_product_edit')]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager,): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {return $this->redirectToRoute('app_login');}
        if(!$product) {return $this->redirectToRoute('app_product_show', ['id'=>$product->getId()]);}

        $productForm = $this->createForm(ProductForm::class, $product);
        $productForm->handleRequest($request);
        if($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('app_product_addImages_product', ['id' => $product->getId()]);
        }
        return $this->render('admin/edit.html.twig', [
            'productForm' => $productForm,
        ]);


    }

    #[Route('/product/delete/{id}', name: 'app_product_delete')]
    public function delete( Product $product, EntityManagerInterface $entityManager): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_login');
        }
        if($product) {
            $entityManager->remove($product);
            $entityManager->flush();

        }


        return $this->redirectToRoute('app_admin_products');
    }



}
