<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class MailerController extends AbstractController
{
    #[Route('/email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        // Create the email object
        $email = (new Email())
            ->from('yassinzemzem1@gmail.com')
            ->to('akoyomi63@gmail.com')    // Replace with your actual email address
            ->subject('Test Email via Mailtrap API')
            ->text('blood donation request recieved')
            ->html('<p>This email was sent via the Mailtrap API in Symfony.</p>');

        // Send the email using the Mailtrap API
        try {
            $mailer->send($email);
            return new Response('Email sent successfully via Mailtrap API!');
        } catch (\Exception $e) {
            return new Response('Error sending email: ' . $e->getMessage());
        }
    }
}
