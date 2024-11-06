<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Videos;
use App\Form\TricksType;
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
            $this->addNewImages($form->get('images')->getData(), $tricks, $entityManager);
            $this->addNewVideos($form->get('videos')->getData(), $tricks, $entityManager);

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

            $deleteImages = $request->get('existing_images_delete', []);
            foreach ($tricks->getImages() as $image) {
                if (isset($deleteImages[$image->getId()])) {
                    $this->deleteImageFile($image);
                    $entityManager->remove($image);
                    $tricks->removeImage($image);
                }
            }

            $replaceImages = $request->files->get('existing_images_replace', []);
            foreach ($tricks->getImages() as $image) {
                if (isset($replaceImages[$image->getId()])) {
                    $this->replaceImageFile($image, $replaceImages[$image->getId()]);
                }
            }


            $deleteVideos = $request->get('existing_videos_delete', []);
            foreach ($tricks->getVideos() as $video) {
                if (isset($deleteVideos[$video->getId()])) {
                    $entityManager->remove($video);
                    $tricks->removeVideo($video);
                }
            }

            $replaceVideos = $request->get('existing_videos_replace', []);
            foreach ($tricks->getVideos() as $video) {
                if (isset($replaceVideos[$video->getId()])) {
                    $cleanedLink = $this->cleanVideoLink($replaceVideos[$video->getId()]);
                    $video->setVideoLink($cleanedLink);
                }
            }

            $this->addNewImages($form->get('images')->getData(), $tricks, $entityManager);
            $this->addNewVideos($form->get('videos')->getData(), $tricks, $entityManager);
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

    private function cleanVideoLink(string $link): string
    {
        if (str_contains($link, 'youtube.com')) {
            parse_str(parse_url($link, PHP_URL_QUERY), $query);
            return isset($query['v']) ? 'https://www.youtube.com/watch?v=' . $query['v'] : $link;
        }

        if (str_contains($link, 'dailymotion.com')) {
            preg_match('/\/video\/([^_]+)/', $link, $matches);
            return isset($matches[1]) ? 'https://www.dailymotion.com/video/' . $matches[1] : $link;
        }

        return $link;
    }

    private function addNewImages(array $newImages, Tricks $tricks, EntityManagerInterface $entityManager): void
    {
        foreach ($newImages as $imageFile) {
            if ($imageFile instanceof UploadedFile) {
                $image = new Images();
                $image->setImageFile($imageFile);
                $image->setTricks($tricks);
                $tricks->addImage($image);
                $entityManager->persist($image);
            }
        }
    }

    private function addNewVideos(array $newVideos, Tricks $tricks, EntityManagerInterface $entityManager): void
    {
        foreach ($newVideos as $videoLink) {
            if ($videoLink) {
                $cleanedLink = $this->cleanVideoLink($videoLink);
                $video = new Videos();
                $video->setVideoLink($cleanedLink);
                $video->setTricks($tricks);
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
}
