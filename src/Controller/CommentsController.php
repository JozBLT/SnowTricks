<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Tricks;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use App\Repository\TricksRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentsController extends AbstractController
{
    #[Route('/load-more-comments', name: 'load_more_comments', methods: ['GET'])]
    public function loadMoreComments(
        Request $request,
        CommentsRepository $commentsRepository,
        TricksRepository $tricksRepository
    ): JsonResponse {
        $tricksId = $request->query->getInt('tricksId');
        $tricks = $tricksRepository->find($tricksId);

        if (!$tricks) {
            return new JsonResponse(['error' => 'Trick non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $comments = $commentsRepository->findBy(['tricks' => $tricks], ['createdAt' => 'DESC'], null, 5);

        return new JsonResponse([
            'html' => $this->renderView('tricks/_comments.html.twig', [
                'comments' => $comments,
                'tricks' => $tricks,
            ]),
        ]);
    }

    #[Route('/tricks/{id}/comment', name: 'comment.create', methods: ['POST'])]
    #[IsGranted('ROLE_VERIFIED')]
    public function create(Request $request, Tricks $tricks, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTricks($tricks);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commentaire a été ajouté.');

            return $this->redirectToRoute('tricks.show', [
                'id' => $tricks->getId(),
                'slug' => $tricks->getSlug(),
            ]);
        }

        return $this->redirectToRoute('tricks.show', [
            'id' => $tricks->getId(),
            'slug' => $tricks->getSlug(),
        ]);
    }
}
