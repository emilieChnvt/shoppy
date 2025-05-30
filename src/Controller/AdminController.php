<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ImagesForm;
use App\Form\ProductForm;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'users'=> $userRepository->findAll(),
        ]);
    }
    private function addRole(User $user, string $role, EntityManagerInterface $manager): void
    {
        $roles = $user->getRoles();

        if (!in_array($role, $roles, true )) {  //true = comparaison structe
            $roles[] = $role;
            $user->setRoles($roles);
            $manager->persist($user);
            $manager->flush();
        }
    } // ajoute que si le user n' pas déjà ce rôle

    #[Route('/makeAdmin/{id}', name: 'app_makeAdmin')]
    public function makeAdmin(User $user, EntityManagerInterface $manager): Response
    {
        $this->addRole($user, 'ROLE_ADMIN', $manager);
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/makeEmployee/{id}', name: 'app_makeEmployee')]
    public function makeEmployee(User $user, EntityManagerInterface $manager): Response
    {
        $this->addRole($user, 'ROLE_EMPLOYEE', $manager);
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/demote/{id}', name: 'app_demote')]
    public function demote(User $user, EntityManagerInterface $manager): Response
    {
        $user->setRoles(['ROLE_USER']); // Reset à ROLE_USER
        $manager->persist($user);
        $manager->flush();
        return $this->redirectToRoute('app_admin');
    }




    #[Route('/products', name: 'app_admin_products')]
    public function showAll(ProductRepository $productRepository): Response
    {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {return $this->redirectToRoute('app_login');}


        return $this->render('admin/products.html.twig', [
            'products'=>$productRepository->findAll(),
        ]);
    }

    #[Route('/product/show/{id}', name: 'app_admin_product')]
    public function show(Product $product): Response
    {
        return $this->render('admin/show.html.twig', [
            'product'=>$product,
        ]);
    }

    #[Route('/products/addImages/{id}', name: 'app_product_addImages_product')]
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

            return $this->redirectToRoute('app_admin_product', ['id' => $product->getId()]);
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
