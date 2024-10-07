<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(TricksRepository $tricksRepository): Response
    {
        $tricks = $tricksRepository->findAll();
        return $this->render('homepage/index.html.twig', [
            'tricks' => $tricks
        ]);
    }
}
