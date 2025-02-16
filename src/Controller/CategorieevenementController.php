<?php

namespace App\Controller;

use App\Entity\Categorieevenement;
use App\Form\CategorieevenementType;
use App\Repository\CategorieevenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categorieevenement')]
final class CategorieevenementController extends AbstractController
{
    #[Route(name: 'app_categorieevenement_index', methods: ['GET'])]
    public function index(CategorieevenementRepository $categorieevenementRepository): Response
    {
        return $this->render('categorieevenement/index.html.twig', [
            'categorieevenements' => $categorieevenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorieevenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieevenement = new Categorieevenement();
        $form = $this->createForm(CategorieevenementType::class, $categorieevenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieevenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorieevenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorieevenement/new.html.twig', [
            'categorieevenement' => $categorieevenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorieevenement_show', methods: ['GET'])]
    public function show(Categorieevenement $categorieevenement): Response
    {
        return $this->render('categorieevenement/show.html.twig', [
            'categorieevenement' => $categorieevenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorieevenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorieevenement $categorieevenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieevenementType::class, $categorieevenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorieevenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorieevenement/edit.html.twig', [
            'categorieevenement' => $categorieevenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorieevenement_delete', methods: ['POST'])]
    public function delete(Request $request, Categorieevenement $categorieevenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorieevenement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorieevenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorieevenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
