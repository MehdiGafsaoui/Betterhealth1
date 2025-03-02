<?php
namespace App\Controller;

use App\Entity\Evenements;
use App\Form\EvenementsType;
use App\Repository\EvenementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/evenements')]
final class EvenementsController extends AbstractController
{
    #[Route(name: 'app_evenements_index', methods: ['GET'])]
    public function index(EvenementsRepository $evenementsRepository): Response
    {
        return $this->render('evenements/index.html.twig', [
            'evenements' => $evenementsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenements_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $params, SluggerInterface $slugger): Response
    {
        $evenement = new Evenements();
        $form = $this->createForm(EvenementsType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification des dates
            $datedebut = $evenement->getDatedebut();
            $datefin = $evenement->getDatefin();
            
            // Vérifier que la date de début est après la date actuelle
            if ($datedebut < new \DateTime()) {
                $this->addFlash('error', 'La date de début doit être après la date actuelle.');
                return $this->render('evenements/new.html.twig', [
                    'evenement' => $evenement,
                    'form' => $form,
                ]);
            }

            // Vérifier que la date de fin est après la date de début
            if ($datefin <= $datedebut) {
                $this->addFlash('error', 'La date de fin doit être après la date de début.');
                return $this->render('evenements/new.html.twig', [
                    'evenement' => $evenement,
                    'form' => $form,
                ]);
            }

            // Gérer l'upload de l'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $params->get('images_directory'),
                        $newFilename
                    );
                    $evenement->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenements_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenements/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenements_show', methods: ['GET'])]
    public function show(Evenements $evenement): Response
    {
        return $this->render('evenements/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenements_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenements $evenement, EntityManagerInterface $entityManager, ParameterBagInterface $params, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EvenementsType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image (si une nouvelle image est fournie)
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $params->get('images_directory'),
                        $newFilename
                    );
                    $evenement->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_evenements_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenements/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenements_delete', methods: ['POST'])]
    public function delete(Request $request, Evenements $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenements_index', [], Response::HTTP_SEE_OTHER);
    }
}
