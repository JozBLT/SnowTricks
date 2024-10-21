<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Tricks;
use App\Form\TricksType;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TricksController extends AbstractController
{
    #[Route('/tricks/{slug}-{id}',
        name: 'tricks.show',
        requirements: ['slug' => '[a-z0-9-]+', 'id' => Requirement::DIGITS],
        methods: ['GET']
    )]
    public function show(string $slug, int $id, TricksRepository $tricksRepository): Response
    {
        $tricks = $tricksRepository->find($id);
        if ($tricks->getSlug() !== $slug) {
            return $this->redirectToRoute('tricks.show', ['slug' => $tricks->getSlug(), 'id' => $tricks->getId()]);
        }
        return $this->render('tricks/show.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/tricks/create', name: 'tricks.create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_VERIFIED')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tricks = new Tricks();
        $form = $this->createForm(TricksType::class, $tricks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $imageFile) {
                if ($imageFile instanceof UploadedFile) {
                    $image = new Images();
                    $image->setImageFile($imageFile);
                    $image->setTricks($tricks);
                    $tricks->addImage($image);
                    $entityManager->persist($image);
                }
            }

            $entityManager->persist($tricks);
            $entityManager->flush();
            $this->addFlash('success', 'Ce tricks a bien été créé !');

            return $this->redirectToRoute('tricks.show', [
                'slug' => $tricks->getSlug(),
                'id' => $tricks->getId()
            ]);
        }

        return $this->render('tricks/create.html.twig', [
            'createForm' => $form
        ]);
    }

    #[Route('/tricks/{id}/edit',
        name: 'tricks.edit',
        requirements: ['id' => Requirement::DIGITS],
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_VERIFIED')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Tricks $tricks): Response
    {
        $form = $this->createForm(TricksType::class, $tricks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Ce tricks a bien été modifié !');

            return $this->redirectToRoute('tricks.show', [
                'slug' => $tricks->getSlug(),
                'id' => $tricks->getId()
            ]);
        }

        return $this->render('tricks/edit.html.twig', [
            'tricks' => $tricks,
            'editForm' => $form
        ]);
    }

    #[Route('/tricks/{id}',
        name: 'tricks.delete',
        requirements: ['id' => Requirement::DIGITS],
        methods: ['DELETE']
    )]
    #[IsGranted('ROLE_VERIFIED')]
    public function delete(EntityManagerInterface $entityManager, Tricks $tricks): Response
    {
        foreach ($tricks->getImages() as $image) {
            $imagePath = $this->getParameter('kernel.project_dir') . '/public/tricks/gallery/' . $image->getImageName();
            if ($image->getImageName() && file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $entityManager->remove($tricks);
        $entityManager->flush();
        $this->addFlash('success', 'Ce Tricks a bien été supprimé');

        return $this->redirectToRoute('homepage');
    }
}
