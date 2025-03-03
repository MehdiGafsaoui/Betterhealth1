<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager
            ->getRepository(Produit::class)
            ->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produitClients', name: 'app_produit_index_client', methods: ['GET'])]
    public function indexClient(EntityManagerInterface $entityManager, Request $request): Response
    {
        $produits = $entityManager->getRepository(Produit::class)->findAll();
        $user = $this->getUser();
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $user]);
        $cartCount = $panier ? $panier->getProduits()->count() : 0;

        return $this->render('produit/indexClient.html.twig', [
            'produits' => $produits,
            'cartCount' => $cartCount
        ]);
    }

    #[Route('/statistics', name: 'app_produit_stats', methods: ['GET'])]
    public function statistics(EntityManagerInterface $entityManager): Response
    {
        // Fetch category counts with category names
        $categoryCounts = $entityManager->createQuery(
            'SELECT c.id, c.nom as category, COUNT(p.id) as count
         FROM App\Entity\Produit p
         JOIN p.categorie c
         GROUP BY c.id'
        )->getResult();

        // Calculate total products
        $totalProducts = $entityManager->getRepository(Produit::class)->count([]);

        // Calculate percentages
        foreach ($categoryCounts as &$category) {
            $category['percentage'] = $totalProducts > 0
                ? round(($category['count'] / $totalProducts) * 100, 2)
                : 0;
        }

        // Debug the data

        return $this->render('admin/statistics.html.twig', [
            'categoryCounts' => $categoryCounts,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,): Response
    {
        $produit = new Produit();
        $user = $this->getUser();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Get the reCAPTCHA response token from the request
            $recaptchaResponse = $request->request->get('recaptcha_response');

            // Validate the reCAPTCHA response
            $recaptcha = new ReCaptcha($_ENV['RECAPTCHA_SECRET_KEY']);
            $recaptchaResult = $recaptcha->verify($recaptchaResponse, $request->getClientIp());

            if (!$recaptchaResult->isSuccess()) {
                // Log the reCAPTCHA errors for debugging
                $errors = $recaptchaResult->getErrorCodes();
                $this->addFlash('error', 'Invalid CAPTCHA. Please try again. Errors: ' . implode(', ', $errors));
                return $this->redirectToRoute('app_produit_new'); // Redirect back to the form
            }

            // reCAPTCHA validation passed
            if ($form->isValid()) {
                $entityManager->persist($produit);
                $entityManager->flush();


            }
            if ($user->getRoles()==['ROLE_ADMIN']) {
                return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('app_produit_index_client', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
            'recaptcha_site_key' => $_ENV['RECAPTCHA_SITE_KEY'], // Pass the site key to the template
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show($id, EntityManagerInterface $entityManager): Response
    {
        // Cast the ID to an integer
        $id = (int)$id;

        // Manually fetch the Produit entity
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        // If the entity is not found, throw a 404 error
        if (!$produit) {
            throw $this->createNotFoundException('Produit not found');
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        // Manually fetch the Produit entity
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        // If the entity is not found, throw a 404 error
        if (!$produit) {
            throw $this->createNotFoundException('Produit not found');
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        // Manually fetch the Produit entity
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        // If the entity is not found, throw a 404 error
        if (!$produit) {
            throw $this->createNotFoundException('Produit not found');
        }

        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route("/produit/get/{id}", name: "app_produit_get")]
    public function getProduit(int $id, EntityManagerInterface $entityManager): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('The product does not exist');
        }

        // Retrieve or create the panier for the connected user
        $user = $this->getUser();
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $user]);
        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $entityManager->persist($panier);
        }

        // Add the product to the panier
        $panier->addProduit($produit);
        $entityManager->flush();


        return $this->redirectToRoute('app_produit_index_client');
    }






}
