<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(TricksRepository $tricksRepository): Response
    {
        $tricks = $tricksRepository->findBy([], ['createdAt' => 'DESC'], 15);

        return $this->render('homepage/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/load-more-tricks', name: 'load_more_tricks', methods: ['GET'])]
    public function loadMore(TricksRepository $tricksRepository, Request $request): JsonResponse
    {
        $offset = $request->query->getInt('offset');
        $tricks = $tricksRepository->findBy([], ['createdAt' => 'DESC'], 10, $offset);
        $hasMore = count($tricks) === 10;
        $html = $this->renderView('homepage/_tricks.html.twig', [
            'tricks' => $tricks
        ]);

        return new JsonResponse([
            'html' => $html,
            'hasMore' => $hasMore,
        ]);
    }
}
