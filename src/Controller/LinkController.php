<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Link;
use App\Service\Scrapper;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class LinkController extends AbstractController
{
    public function form(Request $request, Scrapper $scrapper, ?string $slug = null) {
        if ($slug):
            $post = $this->getPost(['slug' => $slug], ['updateDate' => 'DESC']);
            if (!$post):
                return $this->redirectToRoute('link_new');
            elseif (!$this->getUser() || ($this->getUser() != $post->getAuthor() && !$this->getUser()->isAdmin())):
                return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
            endif;
        else: 
            $post = new Post(); 
            if ($this->getUser()): $post->setAuthor($this->getUser()); endif;
        endif;

        if ($request->isMethod('POST') && $request->get('title') && $request->get('url')):
            // Init parameters
            if ($post->getTitle() != $request->get('title')):
                $post->setTitle($request->get('title'));
                // Generate Slug
                $post->setSlug($post->getTitle());
                while ($duplication = $this->getPost(['slug' => $post->getSlug()])):
                    if (!isset($slug_counter)): $slug_counter = 0; endif;
                    $slug_counter++;
                    $post->setSlug($post->getTitle().'-'.$slug_counter);
                endwhile;
            endif;
            $link = new Link();
            $link->setUrl($request->get('url'));
            $link = $scrapper->getMetadata($link);
            $post->setLink($link);
            $post->setOnline($request->get('online') ? true : false);
            $post->setUpdateDate(new \DateTime());
            $this->getEm()->persist($post);
            $this->getEm()->flush();
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;
        
        return $this->render('app/link_form.html.twig', ['post' => $post]);
    }

}
