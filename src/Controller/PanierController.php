<?php
// src/Controller/PanierController.php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $paniers = $entityManager->getRepository(Panier::class)->findBy(['user' => $user]);
        $panierDetails = [];

        foreach ($paniers as $panier) {
            $products = [];
            foreach ($panier->getProductIds() as $productId) {
                $product = $entityManager->getRepository(Produit::class)->find($productId);
                if ($product) {
                    $products[] = $product;
                }
            }
            $panierDetails[] = [
                'id' => $panier->getId(),
                'user' => $panier->getUser(),
                'products' => $products,
                'status' => $panier->getStatus(),
            ];
        }

        return $this->render('panier/index.html.twig', [
            'paniers' => $panierDetails,
            'panier_count' => count($paniers),
        ]);
    }

    #[Route('/panier/add/{id}', name: 'app_panier_add', methods: ['GET'])]
    public function addProduit(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('The product does not exist');
        }

        $user = $this->getUser();
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $user]);
        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $entityManager->persist($panier);
        }

        $panier->addProduit($produit);
        $entityManager->flush();

        $cartCount = $panier->getProduits()->count();

        return new JsonResponse(['cartCount' => $cartCount]);
    }

    #[Route('/panier/submit', name: 'app_panier_submit', methods: ['GET','POST'])]
    public function submitOrder(EntityManagerInterface $entityManager, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->getUser();
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $user]);

        if ($panier) {
            // Send email to the client
            if($user->getRoles()==['ROLE_ADMIN']) {
                $email = (new Email())
                    ->from($user->getEmail())
                    ->to($panier->getUser()->getEmail())
                    ->subject('Your cart has been submitted')
                    ->text('Your cart has been submitted by the admin.');
            }else{
                $email = (new Email())
                    ->from($panier->getUser()->getEmail())
                    ->to('Admin@exemple.com')
                    ->subject('A new Order has been submitted')
                    ->text('A client has submitted a new order.');

                $mailer->send($email);
            }

            // Optionally, you can clear the cart or perform other actions here
        }

        return $this->redirectToRoute('app_produit_index_client'
        ,['panier' => $panier]);
    }

    #[Route('/panier/accept/{id}', name: 'app_panier_accept', methods: ['GET'])]
    public function accept(int $id, EntityManagerInterface $entityManager, MailerInterface $mailer): RedirectResponse
    {
        $panier = $entityManager->getRepository(Panier::class)->find($id);
        if ($panier) {
            $user = $this->getUser();
            $entityManager->flush();

            // Send email to the client
            $email = (new Email())
                ->from($user->getEmail())
                ->to($panier->getUser()->getEmail())
                ->subject('Your cart has been accepted')
                ->text('Your cart has been accepted by the admin.');

            $mailer->send($email);
        }

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/decline/{id}', name: 'app_panier_decline', methods: ['GET'])]
    public function decline(int $id, EntityManagerInterface $entityManager, MailerInterface $mailer): RedirectResponse
    {
        $panier = $entityManager->getRepository(Panier::class)->find($id);
        if ($panier) {
            $panier->setStatus('Declined');
            $entityManager->flush();
            $user = $this->getUser();

            // Send email to the client
            $email = (new Email())
                ->from($user->getEmail())
                ->to($panier->getUser()->getEmail())
                ->subject('Your cart has been declined')
                ->text('Your cart has been declined by the admin.');

            $mailer->send($email);
        }

        return $this->redirectToRoute('app_panier_index');
    }
}