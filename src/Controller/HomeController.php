<?php
// src/Controller/MailerController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/email', name: 'test_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('contact@emiliechanavat.com')
                ->to('emilie.chnvt@gmail.com')
                ->subject('Test via contrôleur Symfony')
                ->text('Ceci est un test via le contrôleur Symfony')
                ->html('<p>Email envoyé depuis le contrôleur !</p>');

            $mailer->send($email);

            return new Response('Email envoyé avec succès.');
        } catch (\Exception $e) {
            return new Response('Erreur : ' . $e->getMessage());
        }
    }

}