<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Tricks;
use App\Form\CommentsType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentsController extends AbstractController
{
    #[Route('/tricks/{id}/comment', name: 'comment.create', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('tricks.show', ['id' => $tricks->getId(), 'slug' => $tricks->getSlug()]);
        }

        return $this->render('tricks/comments.html.twig', [
            'commentForm' => $form->createView(),
            'tricks' => $tricks,
        ]);
    }
}
