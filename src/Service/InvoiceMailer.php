<?php

namespace App\Service;

use App\Entity\Order;
use Knp\Snappy\Pdf;
use Stripe\Invoice;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class InvoiceMailer
{
    private Pdf $pdf;
    private Environment $twig;
    private MailerInterface $mailer;

    public function __construct(Pdf $pdf, Environment $twig, MailerInterface $mailer)
    {
        $this->pdf = $pdf;
        $this->twig = $twig;
        $this->mailer = $mailer;
    }
    public function send(Order $order): void
    {
        $html = $this->twig->render('pdf/template.html.twig', [
            'order' => $order,
        ]);

        $pdfOutput = $this->pdf->getOutputFromHtml($html);

        $email = (new Email())
            ->from('contact@emiliechanavat.com')
            ->to($order->getCustomer()->getOfUser()->getEmail())
            ->subject('Votre facture - Commande #' . $order->getId())
            ->text('Merci pour votre commande. Votre Facture en pièce jointe.')
            ->attach($pdfOutput, 'facture.pdf', 'application/pdf'); //pdf

        $this->mailer->send($email);

    }


}