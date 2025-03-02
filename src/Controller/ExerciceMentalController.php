<?php

namespace App\Controller;

use App\Entity\ExerciceMental;
use App\Form\ExerciceMentalType;
use App\Repository\ExerciceMentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
#[Route('/exercice/mental')]
final class ExerciceMentalController extends AbstractController
{
    #[Route(name: 'app_exercice_mental_index', methods: ['GET'])]
    public function index(ExerciceMentalRepository $exerciceMentalRepository, Request $request): Response
    {
        $search = $request->query->get('search', '');
        $sortField = $request->query->get('sortField', '');
        $sortDirection = strtoupper($request->query->get('sortDirection', 'ASC'));

        // Validate sortDirection
        if (!in_array($sortDirection, ['ASC', 'DESC'])) {
            $sortDirection = 'ASC';
        }

        $exerciceMentals = $exerciceMentalRepository->findBySearchAndSort($search, $sortField, $sortDirection);

        // If the request is AJAX, render only the table rows
        if ($request->isXmlHttpRequest()) {
            $tableContent = $this->renderView('exercice_mental/_table.html.twig', [
                'exercice_mentals' => $exerciceMentals,
            ]);
            return new JsonResponse(['content' => $tableContent]);
        }

        // Otherwise render the full page
        return $this->render('exercice_mental/index.html.twig', [
            'exercice_mentals' => $exerciceMentals,
        ]);
    }
    #[Route('/new', name: 'app_exercice_mental_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exerciceMental = new ExerciceMental();
        $form = $this->createForm(ExerciceMentalType::class, $exerciceMental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exerciceMental);
            $entityManager->flush();

            return $this->redirectToRoute('app_exercice_mental_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exercice_mental/new.html.twig', [
            'exercice_mental' => $exerciceMental,
            'form' => $form,
        ]);
    }
    
    
    #[Route('/{id}', name: 'app_exercice_mental_show', methods: ['GET'])]
    public function show(ExerciceMental $exerciceMental): Response
    {
        return $this->render('exercice_mental/show.html.twig', [
            'exercice_mental' => $exerciceMental,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_exercice_mental_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExerciceMental $exerciceMental, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExerciceMentalType::class, $exerciceMental);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_exercice_mental_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exercice_mental/edit.html.twig', [
            'exercice_mental' => $exerciceMental,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exercice_mental_delete', methods: ['POST'])]
    public function delete(Request $request, ExerciceMental $exerciceMental, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exerciceMental->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($exerciceMental);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_exercice_mental_index', [], Response::HTTP_SEE_OTHER);
    }
}
