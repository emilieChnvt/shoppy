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


    #[Route('/feedback/{id}/edit', name: 'app_feedback_edit')]
    public function edit(Feedback $feedback, EntityManagerInterface $manager, Request $request): Response
    {
        if (!$this->getUser() || $this->getUser()->getProfile()->getId() !== $feedback->getAuthor()->getId()) {
            return $this->redirectToRoute('app_login');
        }

        if(!$feedback){return $this->redirectToRoute('app_product_show', ['id' => $feedback->getProduct()->getId()]);}

        $feedbackForm = $this->createForm(FeedbackForm::class , $feedback);
        $feedbackForm->handleRequest($request);
        if($feedbackForm->isSubmitted() && $feedbackForm->isValid()){
            $manager->persist($feedback);
            $manager->flush();

            return $this->redirectToRoute('app_product_show', ['id' => $feedback->getProduct()->getId()]);
        }

        return $this->render('feedback/edit.html.twig', [
            'feedbackForm' => $feedbackForm
        ]);
    }

    #[Route('/feedback/{id}/delete', name: 'app_feedback_delete')]
    public function delete(Feedback $feedback, EntityManagerInterface $manager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (!$this->getUser() || $this->getUser()->getProfile()->getId() !== $feedback->getAuthor()->getId()) {
            return $this->redirectToRoute('app_login');
        }
        if($feedback){
            $manager->remove($feedback);
            $manager->flush();
        }

        return $this->redirectToRoute('app_product_show', ['id' => $feedback->getProduct()->getId()]);


    }
}
//pas besoins de show