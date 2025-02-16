<?php
namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RendezVousController extends AbstractController
{
    #[Route('/homepage', name: 'service_page')]
    public function dondesang(): Response
    {
        return $this->render('rendezvous/index.html.twig', [
            'page_title' => 'Service Page',
        ]);
    }
    #[Route('/rendezvous', name: 'rendezvous_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $rendezvous = $em->getRepository(RendezVous::class)->findAll();
        return $this->render('rendezvous/list.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }

    #[Route('/rendezvous/new', name: 'rendezvous_new')]
    public function new(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $rendezvous = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezvous);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if (!$rendezvous->getEmail() || !$rendezvous->getDate()) {
                    $this->addFlash('error', 'You must fill in all the required fields: Email and Date.');
                } else {
                    $em->persist($rendezvous);
                    $em->flush();
                    return $this->redirectToRoute('rendezvous_list');
                }
            } else {
                $this->addFlash('error', 'There are errors in your form. Please check and try again.');
            }
        }

        return $this->render('rendezvous/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }   

    #[Route('/rendezvous/edit/{id}', name: 'rendezvous_edit')]
    public function edit(RendezVous $rendezvous, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezvous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('rendezvous_list');
        }

        return $this->render('rendezvous/edit.html.twig', [
            'form' => $form->createView(),
            'rendezvous' => $rendezvous,
        ]);
    }

    #[Route('/rendezvous/delete/{id}', name: 'rendezvous_delete', methods: ['POST', 'DELETE'])]
    public function delete(RendezVous $rendezvous, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $rendezvous->getId(), $request->request->get('_token'))) {
            $em->remove($rendezvous);
            $em->flush();
        }

        return $this->redirectToRoute('rendezvous_list');
    }

    #[Route('/rendezvous/map', name: 'rendezvous_map')]
    public function map(): Response
    {
        return $this->render('rendezvous/map.html.twig');
    }
}
