<?php

namespace App\Controller;

use App\Entity\Therapie;
use App\Form\TherapieType;
use App\Repository\TherapieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/therapie')]
final class TherapieController extends AbstractController
{
    #[Route(name: 'app_therapie_index', methods: ['GET'])]
    public function index(TherapieRepository $therapieRepository): Response
    {
        return $this->render('therapie/index.html.twig', [
            'therapies' => $therapieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_therapie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $therapie = new Therapie();
        $form = $this->createForm(TherapieType::class, $therapie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($therapie);
            $entityManager->flush();

            return $this->redirectToRoute('app_therapie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('therapie/new.html.twig', [
            'therapie' => $therapie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_therapie_show', methods: ['GET'])]
    public function show(Therapie $therapie): Response
    {
        return $this->render('therapie/show.html.twig', [
            'therapie' => $therapie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_therapie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Therapie $therapie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TherapieType::class, $therapie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_therapie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('therapie/edit.html.twig', [
            'therapie' => $therapie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_therapie_delete', methods: ['POST'])]
    public function delete(Request $request, Therapie $therapie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$therapie->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($therapie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_therapie_index', [], Response::HTTP_SEE_OTHER);
    }
}
