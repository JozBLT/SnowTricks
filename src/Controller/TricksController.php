<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Videos;
use App\Form\CommentsType;
use App\Form\TricksType;
use App\Repository\CommentsRepository;
use App\Repository\TricksRepository;
use DateTimeImmutable;
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
        requirements: ['slug' => Requirement::ASCII_SLUG, 'id' => Requirement::DIGITS],
        methods: ['GET']
    )]
    public function show(
        string $slug,
        int $id,
        TricksRepository $tricksRepository,
        CommentsRepository $commentsRepository
    ): Response {
        $tricks = $tricksRepository->find($id);

        if ($tricks->getSlug() !== $slug) {
            return $this->redirectToRoute('tricks.show', ['slug' => $tricks->getSlug(), 'id' => $tricks->getId()]);
        }
        $comments = $commentsRepository->findBy(['tricks' => $tricks], ['createdAt' => 'DESC'], 5);
        $commentForm = $this->createForm(CommentsType::class);

        return $this->render('tricks/show.html.twig', [
            'tricks' => $tricks,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
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
            $tricks->setCreatedBy($this->getUser());

            if ($request->files->get('existing_thumbnail_replace')) {
                $thumbnailFile = $request->files->get('existing_thumbnail_replace');
                $tricks->setThumbnailFile($thumbnailFile);
            }

            $this->addMedia($form->get('images')->getData(), $tricks, $entityManager, 'image');
            $this->addMedia($form->get('videos')->getData(), $tricks, $entityManager, 'video');

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
            if ($request->files->get('existing_thumbnail_replace')) {
                $thumbnailFile = $request->files->get('existing_thumbnail_replace');
                $tricks->setThumbnailFile($thumbnailFile);
            }

            if ($request->get('existing_thumbnail_delete')) {
                $thumbnailPath = $this->getParameter('kernel.project_dir') . '/public/tricks/thumbnail/' . $tricks->getThumbnail();
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }
                $tricks->setThumbnail(null);
            }

            $this->handleImageModifications($request, $tricks, $entityManager);
            $this->handleVideoModifications($request, $tricks, $entityManager);
            $this->addMedia($form->get('images')->getData(), $tricks, $entityManager, 'image');
            $this->addMedia($form->get('videos')->getData(), $tricks, $entityManager, 'video');
            $entityManager->flush();
            $this->addFlash('success', 'Le trick a été mis à jour avec succès.');

            return $this->redirectToRoute('tricks.show', [
                'id' => $tricks->getId(),
                'slug' => $tricks->getSlug(),
            ]);
        }

        return $this->render('tricks/edit.html.twig', [
            'tricks' => $tricks,
            'editForm' => $form->createView(),
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

    private function addMedia(array $newMedia, Tricks $tricks, EntityManagerInterface $entityManager, string $type): void
    {
        foreach ($newMedia as $mediaFile) {
            if ($type === 'image' && $mediaFile instanceof UploadedFile) {
                $image = new Images();
                $image->setImageFile($mediaFile);
                $tricks->addImage($image);
                $entityManager->persist($image);
            } elseif ($type === 'video' && !empty($mediaFile)) {
                $video = new Videos();
                $video->setVideoLink($mediaFile);
                $tricks->addVideo($video);
                $entityManager->persist($video);
            }
        }
    }

    private function deleteImageFile(Images $image): void
    {
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/tricks/gallery/' . $image->getImageName();
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    private function replaceImageFile(Images $image, UploadedFile $newImageFile): void
    {
        $this->deleteImageFile($image);
        $image->setImageFile($newImageFile);
        $image->setUpdatedAt(new DateTimeImmutable());
    }

    private function handleImageModifications(Request $request, Tricks $tricks, EntityManagerInterface $entityManager): void
    {
        $deleteImages = $request->get('existing_images_delete', []);
        $replaceImages = $request->files->get('existing_images_replace', []);

        foreach ($tricks->getImages() as $image) {

            if (isset($deleteImages[$image->getId()])) {
                $this->deleteImageFile($image);
                $entityManager->remove($image);
                $tricks->removeImage($image);
            }

            if (isset($replaceImages[$image->getId()]) && $replaceImages[$image->getId()] instanceof UploadedFile) {
                $this->replaceImageFile($image, $replaceImages[$image->getId()]);
            }
        }
    }

    private function handleVideoModifications(Request $request, Tricks $tricks, EntityManagerInterface $entityManager): void
    {
        $deleteVideos = $request->get('existing_videos_delete', []);
        $replaceVideos = $request->get('existing_videos_replace', []);

        foreach ($tricks->getVideos() as $video) {

            if (isset($deleteVideos[$video->getId()])) {
                $entityManager->remove($video);
                $tricks->removeVideo($video);
            }

            if (isset($replaceVideos[$video->getId()])) {
                $video->setVideoLink($replaceVideos[$video->getId()]);
            }
        }
    }
}
