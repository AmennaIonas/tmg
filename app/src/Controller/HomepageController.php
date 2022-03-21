<?php

namespace App\Controller;


use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     */
    #[Route('/homepage', name: 'app_homepage')]
    public function index(
        Request $request,
        MovieService $movieService
    ): Response
    {
        $page = $request->query->get('page') ?: 1;

        [$paginatedMovies, $totalPages, $currentPage] = $movieService->getPaginatedMovies($page);

        unset($object);

        return $this->render('homepage/index.html.twig', [
            'paginatedMovies' => $paginatedMovies,
            'current_page' => $currentPage,
            'max_page' => $totalPages,
        ]);
    }
}
