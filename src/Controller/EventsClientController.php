<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Entity\Participation;
use App\Repository\EvenementsRepository;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



final class EventsClientController extends AbstractController
{
    #[Route('/events/client', name: 'app_events_client')]
    public function index(
        EvenementsRepository $evenementsRepository, 
        PaginatorInterface $paginator, 
        Request $request
    ): Response {
        // Récupérer le paramètre de tri depuis l'URL (par défaut : 'asc' pour tri ascendant)
        $sort = $request->query->get('sort', 'asc');

        // Vérification de la validité de la valeur du tri
        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'asc';  // Valeur par défaut si tri invalide
        }

        // Construire la requête en fonction du tri
        $query = $evenementsRepository->createQueryBuilder('e')
            ->orderBy('e.Datedebut', $sort) // Tri par date
            ->getQuery();

        // Paginer les résultats : 6 événements par page
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Numéro de page depuis l'URL
            6 // Limite par page
        );

        return $this->render('events_client/index.html.twig', [
            'pagination' => $pagination,
            'sort' => $sort,  // Passer le paramètre de tri à la vue
        ]);
    }
   
    #[Route('/events/participate/{id}', name: 'app_evenements_participate')]
    public function participate(Evenements $evenement): Response
    {
        // Vérification que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour participer à un événement.');
            return $this->redirectToRoute('app_events_client');
        }

        return $this->redirectToRoute('app_confirmation', ['id' => $evenement->getId()]);
    }



    #[Route('/confirmation/{id}', name: 'app_confirmation')]
    public function confirmation(Evenements $evenement, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_events_client');
        }

        // Récupérer le nombre de places réservées par l'utilisateur pour cet événement
        $participation = $entityManager->getRepository(Participation::class)
            ->findOneBy(['participant' => $user, 'evenement' => $evenement]);

        $nombrePlaces = $participation ? $participation->getNombrepersonne() : 0;

        return $this->render('events_client/confirmation.html.twig', [
            'evenement' => $evenement,
            'nombrePlaces' => $nombrePlaces, // Passer le nombre de places réservées
        ]);
    }

    #[Route('/reserver/{id}', name: 'app_reserver', methods: ['POST'])]
    public function reserver(Request $request, Evenements $evenement, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour réserver des places.');
            return $this->redirectToRoute('app_events_client');
        }
    
        $nombrePlaces = (int)$request->request->get('nombre_places', 1);
        if ($nombrePlaces <= 0) {
            $this->addFlash('error', 'Le nombre de places doit être supérieur à zéro.');
            return $this->redirectToRoute('app_confirmation', ['id' => $evenement->getId()]);
        }
    
        $participation = $entityManager->getRepository(Participation::class)
            ->findOneBy(['participant' => $user, 'evenement' => $evenement]);
    
        if ($participation) {
            $participation->setNombrepersonne($nombrePlaces);
        } else {
            $participation = new Participation();
            $participation->setParticipant($user);
            $participation->setEvenement($evenement);
            $participation->setNombrepersonne($nombrePlaces);
            $participation->setDateparticipation(new \DateTime());
        }
    
        $entityManager->persist($participation);
        $entityManager->flush();
    
        $userEmail = $user->getEmail();
    
        // Envoi de l'email avec les détails de l'événement
        $mailerService = new MailerService(new Mailer(Transport::fromDsn('gmail://firas.guesmi93806411@gmail.com:kyld%20wmzb%20finx%20dmns@default')));
        $mailerService->sendEmail($evenement, $userEmail);
    
        $this->addFlash('success', 'Votre réservation à l\'événement "' . $evenement->getNom() . '" a été enregistrée avec succès. Un email vous a été envoyé.');
    
        return $this->redirectToRoute('app_confirmation', ['id' => $evenement->getId()]);
    }
    
    
   
    
    #[Route('/events/details/{id}', name: 'app_evenements_details', methods: ['GET'])]
    public function details(Evenements $evenement): Response
    {
        return $this->render('events_client/details.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/export-pdf/{id}', name: 'app_export_pdf')]
    public function exportPdf(Evenements $evenement, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour exporter le PDF.');
            return $this->redirectToRoute('app_events_client');
        }

        // Récupérer le nombre de places réservées
        $participation = $entityManager->getRepository(Participation::class)
            ->findOneBy(['participant' => $user, 'evenement' => $evenement]);

        $nombrePlaces = $participation ? $participation->getNombrepersonne() : 0;

        // Options de Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');

        // Initialiser Dompdf
        $dompdf = new Dompdf($options);

        // Générer le HTML pour le PDF
        $html = $this->renderView('events_client/pdf_template.html.twig', [
            'evenement' => $evenement,
            'nombrePlaces' => $nombrePlaces,
            'user' => $user,
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Rendre le PDF
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourner le PDF en réponse
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="confirmation_participation.pdf"',
            ]
        );
    }
}