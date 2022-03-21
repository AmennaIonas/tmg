<?php

namespace App\Service;

class MovieApiService
{
    private string $apiUrl;

    public function __construct(
        string $apiUrl
    )
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return array
     * @throws \JsonException
     */
    public function getMoviesFromApi(): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true, 512, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}