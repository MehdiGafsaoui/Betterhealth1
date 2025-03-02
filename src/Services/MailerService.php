<?php

namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    // Injection du service MailerInterface via le constructeur
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    // Méthode pour envoyer un email de confirmation de participation
    public function sendEmail($code, $email): void
    {
        // Création de l'email
        $email = (new Email())
            ->from('saropez.pro@gmail.com')  // L'adresse email d'envoi
            ->to($email)  // L'adresse email du destinataire
            ->subject('Confirmation de participation à l\'événement')  // Sujet de l'email
            ->text('Vous avez bien participé à l\'événement. Votre code de confirmation est : ' . $code) // Corps de l'email en texte brut
            ->html('<p>Vous avez bien participé à l\'événement. <strong>Votre code de confirmation est : ' . $code . '</strong></p>');  // Corps de l'email en HTML
    
        // Envoi de l'email
        $this->mailer->send($email);
    }
    
    
}
