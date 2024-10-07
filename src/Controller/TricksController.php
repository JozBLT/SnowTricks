<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Form\TricksType;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TricksController extends AbstractController
{
    #[Route('/tricks/{slug}-{id}', name: 'tricks.show', requirements: ['slug' => '[a-z0-9-]+', 'id' => '\d+'])]
    public function show(string $slug, int $id, TricksRepository $tricksRepository): Response
    {
        $trick = $tricksRepository->find($id);
        if ($trick->getSlug() !== $slug) {
            return $this->redirectToRoute('tricks.show', ['slug' => $trick->getSlug(), 'id' => $trick->getId()]);
        }
        return $this->render('tricks/show.html.twig', [
            'trick' => $trick
        ]);
    }

    #[Route('/tricks/create', name: 'tricks.create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tricks = new Tricks();
        $form = $this->createForm(TricksType::class, $tricks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    #[Route('/tricks/{id}/edit', name: 'tricks.edit', methods: ['GET', 'POST'])]
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
            'trick' => $tricks,
            'editForm' => $form
        ]);
    }

    #[Route('/tricks/{id}', name: 'tricks.delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Tricks $tricks): Response
    {
        $entityManager->remove($tricks);
        $entityManager->flush();
        $this->addFlash('success', 'Ce Tricks a bien été supprimé');

        return $this->redirectToRoute('homepage');
    }
}
