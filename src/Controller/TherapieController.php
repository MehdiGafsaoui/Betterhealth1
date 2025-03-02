<?php

namespace App\Controller;

use App\Entity\Therapie;
use App\Form\TherapieType;
use App\Repository\TherapieRepository;
use App\Service\QrCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
#[Route('/therapie')]
final class TherapieController extends AbstractController
{
    private QrCodeGenerator $qrCodeGenerator;
    public function __construct(QrCodeGenerator $qrCodeGenerator)
    {
        $this->qrCodeGenerator = $qrCodeGenerator;
    }
    #[Route(name: 'app_therapie_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $therapies=$entityManager->getRepository(Therapie::class)->findAll();
        foreach ($therapies as $therapie){
            $content=$this->buildTherapieQrContent($therapie);
            $qrCodePath=$this->qrCodeGenerator->generate($content,$therapie->getId(),'therapie');
            $therapie->qrCodePath=$qrCodePath;
        }
        return $this->render('therapie/index.html.twig', [
            'therapies' => $therapies,
        ]);
    }
    private function buildTherapieQrContent(Therapie $therapie): string
    {
        $content="Therapie information\n";
        $content.="Nom: ".$therapie->getNom()."\n";
        $content.="Description: ".$therapie->getDescription()."\n";
        $content.="Objectif: ".$therapie->getObjectif()."\n";
        $content.="Durée: ".$therapie->getDuree()."\n";
        $content.="Type: ".$therapie->getType()."\n";
        return $content;
    }
    #[Route('/front',name: 'app_therapie_index_front', methods: ['GET'])]
    public function indexFront(TherapieRepository $therapieRepository): Response
    {
        return $this->render('therapie/frontindex.html.twig', [
            'therapies' => $therapieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_therapie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $therapie = new Therapie();
        $form = $this->createForm(TherapieType::class, $therapie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    // Handle file upload exception if needed
                    $this->addFlash('error', 'Image upload failed.');
                }
                $therapie->setImage($newFilename);

            }
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
    public function edit(Request $request, Therapie $therapie, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TherapieType::class, $therapie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    // Handle file upload exception if needed
                    $this->addFlash('error', 'Image upload failed.');
                }
                $therapie->setImage($newFilename);

            }
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
