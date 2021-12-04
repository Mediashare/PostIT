<?php

namespace App\Service;

use App\Entity\Link;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

Class Scrapper {
    public function getMetadata(Link $link): ?Link {
        try {
            $client = new Client();
            $response = $client->get($link->getUrl());
        } catch(\GuzzleHttp\Exception\RequestException $e) {
            $error = null;
            // you can catch here 400 response errors and 500 response errors
            // You can either use logs here use Illuminate\Support\Facades\Log;
            $error['error'] = $e->getMessage();
            $error['request'] = $e->getRequest();
            if($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '400'){
                    $error['response'] = $e->getResponse(); 
                }
            }
            return false;
        }

        $crawler = new Crawler($response->getBody()->getContents());
        $link->setTitle($crawler->filter("title")->text() ?? null);
        $link->setDescription($crawler->filter("meta[name='description']")->eq(0)->attr('content')?? null);
        $image = $crawler->filter("img")->eq(0)->attr('src') ?? null;
        if (str_starts_with($image, '/')):
            $image = 'http://' . parse_url($link->getUrl(), PHP_URL_HOST) . $image;
        endif;
        $link->setImage($image);
        return $link;
    }
}