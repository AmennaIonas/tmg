<?php

namespace App\Service;

class MovieService
{
    private int $itemsPerPage;
    private MovieCacheService $movieCacheService;
    private MovieApiService $movieApiService;


    public function __construct(
        MovieCacheService $movieCacheService,
        MovieApiService   $movieApiService,
        int               $itemsPerPage
    ) {
        $this->itemsPerPage = $itemsPerPage;
        $this->movieCacheService = $movieCacheService;
        $this->movieApiService = $movieApiService;
    }

    public function getPaginatedMovies($page): array
    {
        $movies = $this->movieApiService->getMoviesFromApi();
        $totalPagesCount = $this->getTotalPagesCount(count($movies));

        if($page < 1 || $page > $totalPagesCount) {
            $page = 1;
        }

        $paginatedMovies = array_slice($movies, $this->itemsPerPage * ($page - 1), $this->itemsPerPage);

        foreach($paginatedMovies as &$object) {
            if(array_key_exists('cardImages', $object)) {
                $object['cardImages'] = $this->getImages($object['cardImages']);
            }

            if(array_key_exists('keyArtImages', $object)) {
                $object['keyArtImages'] = $this->getImages($object['keyArtImages']);
            }
        }

        return [$paginatedMovies, $totalPagesCount, $page];
    }

    public function getTotalPagesCount($totalCount): float
    {
        return ceil($totalCount / $this->itemsPerPage);
    }

    public function getImages(array $images): array
    {
        foreach ($images as &$image) {
            $url = $image['url'];
            $urlCacheName = basename($url);
            $image = $this->movieCacheService->getImageFromCacheAndSave($urlCacheName, $image, $url);
        }

        return $images;
    }
}