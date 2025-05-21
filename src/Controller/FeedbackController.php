<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\Product;
use App\Form\FeedbackForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FeedbackController extends AbstractController
{
    #[Route('/feedback', name: 'app_feedback')]
    public function index(): Response
    {
        return $this->render('feedback/index.html.twig', [
            'controller_name' => 'FeedbackController',
        ]);
    }

    #[Route('/feedback/create/{id}', name: 'app_feedback_create')]
    public function create(EntityManagerInterface $manager, Request $request, Product $product): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $feedback = new Feedback();
        $feedbackForm = $this->createForm(FeedbackForm::class , $feedback);
        $feedbackForm->handleRequest($request);
        if($feedbackForm->isSubmitted() && $feedbackForm->isValid()){
            $feedback->setAuthor($this->getUser()->getProfile());
            $feedback->setCreateAt(new \DateTimeImmutable());
            $feedback->setProduct($product);
            $manager->persist($feedback);
            $manager->flush();
            return $this->redirectToRoute('app_product_show', ['id' => $feedback->getProduct()->getId()]);
        }

        return $this->render('feedback/create.html.twig', [
            'feedbackForm' => $feedbackForm->createView(),

        ]);

    }
}
