<?php

namespace App\Controller;

use App\Entity\CentreDeDon;
use App\Form\CentreDeDonType;
use App\Repository\CentreDeDonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/centre/de/don')]
final class CentreDeDonController extends AbstractController
{
    #[Route(name: 'app_centre_de_don_index', methods: ['GET'])]
    public function index(CentreDeDonRepository $centreDeDonRepository): Response
    {
        return $this->render('centre_de_don/index.html.twig', [
            'centre_de_dons' => $centreDeDonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_centre_de_don_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $centreDeDon = new CentreDeDon();
        $form = $this->createForm(CentreDeDonType::class, $centreDeDon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure createdAt is set if it's null
            if (!$centreDeDon->getCreatedAt()) {
                $centreDeDon->setCreatedAt(new \DateTimeImmutable());
            }

            $entityManager->persist($centreDeDon);
            $entityManager->flush();

            $this->addFlash('success', 'Le centre de don a été créé avec succès.');

            return $this->redirectToRoute('app_centre_de_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('centre_de_don/new.html.twig', [
            'centre_de_don' => $centreDeDon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_centre_de_don_show', methods: ['GET'])]
    public function show(CentreDeDon $centreDeDon): Response
    {
        return $this->render('centre_de_don/show.html.twig', [
            'centre_de_don' => $centreDeDon,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_centre_de_don_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CentreDeDon $centreDeDon, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CentreDeDonType::class, $centreDeDon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_centre_de_don_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('centre_de_don/edit.html.twig', [
            'centre_de_don' => $centreDeDon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_centre_de_don_delete', methods: ['POST'])]
    public function delete(Request $request, CentreDeDon $centreDeDon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$centreDeDon->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($centreDeDon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_centre_de_don_index', [], Response::HTTP_SEE_OTHER);
    }
}
