<?php

namespace App\Tests;

use App\Service\MovieApiService;
use App\Service\MovieCacheService;
use App\Service\MovieService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

class MovieServiceTest extends TestCase
{
    public MockObject|CacheInterface|null $photosCacheMock;
    public MovieService|null $movieService;

    public function setUp(): void
    {
        $itemsPerPage = 10;
        $this->movieCacheService = $this->getMockBuilder(MovieCacheService::class)->disableOriginalConstructor()->getMock();
        $this->movieCacheService->method('getImageFromCacheAndSave')->willReturn(json_decode('[{"url":"https:\/\/mgtechtest.blob.core.windows.net\/images\/unscaled\/2012\/11\/29\/Parental-Guidance-VPA.jpg","h":1004,"w":768,"hit":"from cache","cachedImage":"c29tZSByYW5kb20gc3RyaW5n"}]', true));
        $this->movieApiService = $this->getMockBuilder(MovieApiService::class)->disableOriginalConstructor()->getMock();

        $this->movieService = new MovieService(
            $this->movieCacheService,
            $this->movieApiService,
            $itemsPerPage
        );

    }

    public function tearDown(): void
    {
        $this->photosCacheMock = null;
        $this->movieService = null;
    }

    public function testGetTotalPagesCount(): void
    {
        $this->assertEquals(3, $this->movieService->getTotalPagesCount(30));
        $this->assertEquals(4, $this->movieService->getTotalPagesCount(31));
        $this->assertEquals(4, $this->movieService->getTotalPagesCount(39));
    }

    public function testGetImages(): void
    {
        $images = json_decode('[{"url":"https:\/\/mgtechtest.blob.core.windows.net\/images\/unscaled\/2012\/11\/29\/Parental-Guidance-VPA.jpg","h":1004,"w":768}]', true);
        $expectedResult = json_decode('[[{"url":"https:\/\/mgtechtest.blob.core.windows.net\/images\/unscaled\/2012\/11\/29\/Parental-Guidance-VPA.jpg","h":1004,"w":768,"hit":"from cache","cachedImage":"c29tZSByYW5kb20gc3RyaW5n"}]]', true);

        $result = $this->movieService->getImages($images);
        $this->assertEquals($expectedResult, $result);
    }
}
