<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;

class MovieCacheService
{

    private CacheInterface $photosCache;

    public function __construct(
        CacheInterface $photosCache
    ) {
        $this->photosCache = $photosCache;
    }

    /**
     * @param string $urlCacheName
     * @param array $image
     * @param string $url
     * @return mixed
     */
    public function getImageFromCacheAndSave(string $urlCacheName, array $image, string $url): array
    {
        $cacheItem = $this->photosCache->getItem($urlCacheName);
        $image['hit'] = 'not from cache';

        if (!$cacheItem->isHit()) {
            try {
                $fileContents = file_get_contents($url);
            } catch (\Exception $exception) {
                $image['hit'] = 'File does not exist, cache not saved';
            }

            if (isset($fileContents) && $fileContents) {
                $imageData = base64_encode($fileContents);

                $cacheItem->set($imageData);
                $this->photosCache->save($cacheItem);
                $image['cachedImage'] = $imageData;
                $image['hit'] = 'saved now in cache';
            }
        } else {
            $imageData = $cacheItem->get();
            $image['cachedImage'] = $imageData;
            $image['hit'] = 'from cache';
        }

        return $image;
    }
}