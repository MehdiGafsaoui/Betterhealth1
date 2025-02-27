<?php

namespace App\Controller;

use App\Entity\DemandeDonSang;
use App\Form\DemandeDonSangType;
use App\Repository\DemandeDonSangRepository;
use App\Repository\CentreDeDonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


#[Route('/demande/don/sang')]
final class DemandeDonSangController extends AbstractController
{
    #[Route(name: 'app_demande_don_sang_index', methods: ['GET'])]
    public function index(Request $request, DemandeDonSangRepository $demandeDonSangRepository ,CentreDeDonRepository $centreDeDonRepository,): Response
    { $demandeDonSangs = $demandeDonSangRepository->findAll();

        // Get the status from the request query parameters
        $status = $request->query->get('status');
        $user = $this->getUser();
        $showPersonalRequests = $request->query->get('personal') === 'true';

        // Fetch demandeDonSangs based on the selected filter
        if ($showPersonalRequests && $user) {
            // Filter by user (personal requests)
            $demandeDonSangs = $demandeDonSangRepository->findByUser($user->getId());
        } elseif ($status) {
            // Filter by status
            $demandeDonSangs = $demandeDonSangRepository->findByStatus($status);
        } else {
            // No filter: show all
            $demandeDonSangs = $demandeDonSangRepository->findAll();
        }
    
        return $this->render('demande_don_sang/index.html.twig', [
            'demande_don_sangs' => $demandeDonSangs,
            'showPersonalRequests' => $showPersonalRequests,
            'selectedStatus' => $status,
        ]);
    }
    #[Route('/new', name: 'app_demande_don_sang_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,MailerInterface $mailer ): Response
    {
        $demandeDonSang = new DemandeDonSang();
        $demandeDonSang->setStatus('pending');
        $user = $this->getUser();
        $demandeDonSang->setUser($user);
        $form = $this->createForm(DemandeDonSangType::class, $demandeDonSang);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demandeDonSang);
            $entityManager->flush();
            

            return $this->redirectToRoute('app_demande_don_sang_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande_don_sang/new.html.twig', [
            'demande_don_sang' => $demandeDonSang,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_don_sang_show', methods: ['GET'])]
    public function show(DemandeDonSang $demandeDonSang): Response
    {
        return $this->render('demande_don_sang/show.html.twig', [
            'demande_don_sang' => $demandeDonSang,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_don_sang_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DemandeDonSang $demandeDonSang, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeDonSangType::class, $demandeDonSang);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_don_sang_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande_don_sang/edit.html.twig', [
            'demande_don_sang' => $demandeDonSang,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_don_sang_delete', methods: ['POST'])]
    public function delete(Request $request, DemandeDonSang $demandeDonSang, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demandeDonSang->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($demandeDonSang);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demande_don_sang_index', [], Response::HTTP_SEE_OTHER);
    }


    
}
