<?php

namespace App\Services;

use App\Entity\Evenements;
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
    public function sendEmail(Evenements $evenement, string $email): void
{
    $emailMessage = (new Email())
        ->from('saropez.pro@gmail.com')
        ->to($email)
        ->subject('Confirmation de participation à l\'événement : ' . $evenement->getNom())
        ->text(
            "Détails de l'événement :\n\n" .
            "Nom : " . $evenement->getNom() . "\n" .
            "Date de début : " . $evenement->getDatedebut()->format('d/m/Y') . "\n" .
            "Date de fin : " . $evenement->getDatefin()->format('d/m/Y') . "\n" .
            "Lieu : " . $evenement->getLieu() . "\n\n" .
            "Description : " . $evenement->getDescription()
        )
        ->html("
            <p><strong>Détails de l'événement :</strong></p>
            <ul>
                <li><strong>Nom :</strong> {$evenement->getNom()}</li>
                <li><strong>Date de début :</strong> " . $evenement->getDatedebut()->format('d/m/Y') . "</li>
                <li><strong>Date de fin :</strong> " . $evenement->getDatefin()->format('d/m/Y') . "</li>
                <li><strong>Lieu :</strong> {$evenement->getLieu()}</li>
            </ul>
            <p><strong>Description :</strong></p>
            <p>{$evenement->getDescription()}</p>
        ");

    $this->mailer->send($emailMessage);
}

    
    
}