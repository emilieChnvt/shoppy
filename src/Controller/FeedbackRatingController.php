<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\FeedBackRating;
use App\Form\FeedbackRatingForm;
use App\Repository\FeedBackRatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FeedbackRatingController extends AbstractController
{

    #[Route('/feedback/rating/{id}', name: 'app_feedback_rating')]
    public function create(FeedBackRatingRepository $feedBackRatingRepository,  EntityManagerInterface $entityManager, Request $request, Feedback $feedback): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        if($this->getUser()->getProfile() ===  $feedback->getAuthor()->getId()){
            return $this->redirectToRoute('app_product_show',['id' => $feedback->getProduct()->getId()]);
        }

        $alreadyRated = $feedBackRatingRepository->findOneBy(['feedback' => $feedback, 'author' => $this->getUser()->getProfile()]);
        if($alreadyRated){
            return $this->redirectToRoute('app_product_show',['id' => $feedback->getProduct()->getId()]);

        }
        $feedbackRating = new FeedbackRating();
        $form = $this->createForm(FeedbackRatingForm::class , $feedbackRating);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $feedbackRating->setAuthor($this->getUser()->getProfile());
            $feedbackRating->setFeedback($feedback);
            $entityManager->persist($feedbackRating);
            $entityManager->flush();
            return $this->redirectToRoute('app_product_show',['id' => $feedback->getProduct()->getId()]);
        }
        return $this->render('feedback_rating/create.html.twig', [
            'form' => $form->createView(),
            'feedback' => $feedback,
        ]);



    }
}
