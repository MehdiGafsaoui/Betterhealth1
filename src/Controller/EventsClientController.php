<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Entity\Participation;
use App\Repository\EvenementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EventsClientController extends AbstractController
{
    #[Route('/events/client', name: 'app_events_client')]
    public function index(EvenementsRepository $evenementsRepository): Response
    {
        return $this->render('events_client/index.html.twig', [
            'evenements' => $evenementsRepository->findAll(),
        ]);
    }

    #[Route('/events/participate/{id}', name: 'app_evenements_participate')]
    public function participate(Evenements $evenement, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            // Si l'utilisateur n'est pas connecté, afficher un message d'erreur
            $this->addFlash('error', 'Vous devez être connecté pour participer à un événement.');
            return $this->redirectToRoute('app_events_client'); // Rediriger vers la liste des événements
        }

        // Vérifier si l'utilisateur a déjà participé à cet événement
        foreach ($evenement->getParticipations() as $participation) {
            if ($participation->getParticipant() === $user) {
                $this->addFlash('warning', 'Vous avez déjà participé à cet événement.');
                return $this->redirectToRoute('app_events_client');
            }
        }

        // Création de la participation
        $participation = new Participation();
        $participation->setParticipant($user);
        $participation->setEvenement($evenement);
        $participation->setNombrepersonne(1);
        $participation->setDateparticipation(new \DateTime());

        // Sauvegarde en base de données
        $entityManager->persist($participation);
        $entityManager->flush();

        // Redirection vers la page de confirmation avec l'ID de l'événement
        return $this->redirectToRoute('app_confirmation', ['id' => $evenement->getId()]);
    }

    #[Route('/confirmation/{id}', name: 'app_confirmation')]
    public function confirmation(Evenements $evenement): Response
    {
        // Afficher la page de confirmation avec les détails de l'événement
        return $this->render('events_client/confirmation.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}   