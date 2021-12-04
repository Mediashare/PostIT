<?php

namespace App\Service;

use App\Entity\Link;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\DomCrawler\Crawler;

Class Scrapper {
    public function getMetadata(Link $link): ?Link {
        try {
            $client = new Client();
            $response = $client->get($link->getUrl());
        } catch(ClientException | ServerException | ConnectException $e) {
            $error = null;
            // you can catch here 400 response errors and 500 response errors
            // You can either use logs here use Illuminate\Support\Facades\Log;
            $error['error'] = $e->getMessage();
            $error['request'] = $e->getRequest();
            return null;
        }

        $crawler = new Crawler($response->getBody()->getContents());
        
        $link->setTitle($crawler->filter("title")->text() ?? null);

        if ($crawler->filter("meta[name='description']")->count() > 0):
            $link->setDescription($crawler->filter("meta[name='description']")->eq(0)->attr('content') ?? null);
        endif;

        $image = $crawler->filter("img")->count();
        if ($image > 0 && str_starts_with($image = $crawler->filter("img")->eq(0)->attr('src'), '/')):
            $image = 'http://' . parse_url($link->getUrl(), PHP_URL_HOST) . $image;
        elseif (!$image): $image = null; endif;
        $link->setImage($image ?? null);
        
        return $link;
    }
}