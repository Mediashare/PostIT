<?php

namespace App\Controller;

use App\Entity\Post;
use App\Controller\AbstractController;
use App\Entity\Article;
use App\Entity\Link;
use App\Service\Scrapper;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController {    
    public function show(string $slug) {
        $post = $this->getPost(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        if (!$post->isOnline() && 
            (!$this->getUser() || $this->getUser() !== $post->getAuthor())):
            return $this->redirectToRoute('index');
        endif;
        
        $post->setViews($post->getViews() + 1);
        $this->getEm()->persist($post);
        $this->getEm()->flush();
        
        return $this->render('app/post.html.twig', ['post' => $post]);
    }

    public function edit(string $slug) {
        $post = $this->getPost(['slug' => $slug], ['updateDate' => 'DESC']);
        if (!$post):
            return $this->redirectToRoute('app');
        elseif (!$this->getUser() || ($this->getUser() != $post->getAuthor() && !$this->getUser()->isAdmin())):
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;

        if ($post->getArticle()): 
            return $this->redirectToRoute('article_form', ['slug' => $post->getSlug()]);
        elseif ($post->getLink()): 
            return $this->redirectToRoute('link_form', ['slug' => $post->getSlug()]);
        endif;
    }

    public function upload(Request $request) {
        $user = $this->getUser(['apikey' => $request->headers->get('ApiKey')]);
        if ($user):
            $title = $request->get('title');
            $content = $request->get('content') ?? $this->getFileContent($request->files->get('content'));
            $url = $request->get('url');
            if ($title && ($url || $content)):
                $post = new Post();
                $post->setTitle($title);
                if ($content):
                    $article = new Article();
                    $article->setContent($content);
                    $post->setArticle($article);
                elseif ($url):
                    $link = new Link();
                    $link->setUrl($url);
                    $scrapper = new Scrapper();
                    $link = $scrapper->getMetadata($link);
                    $post->setLink($link);
                endif;
                if (!$request->get('online') || $request->get('online') === "false"):
                    $online = false; else: $online = true;
                endif;
                $post->setOnline($online);
                // Generate Slug
                $post->setSlug($post->getTitle());
                while ($duplication = $this->getPost(['slug' => $post->getSlug()])):
                    if (!isset($slug_counter)): $slug_counter = 0; endif;
                    $slug_counter++;
                    $post->setSlug($post->getTitle().'-'.$slug_counter);
                endwhile;
                // Check Apikey / Author
                $post->setAuthor($user);
                $this->getEm()->persist($post);
                $this->getEm()->flush();
                return $this->json(['status' => 'success', 'url' => $this->generateUrl('post', ['slug' => $post->getSlug()], false)]);
            endif;
        endif;
        return $this->json(['status' => 'error']);
    }

    public function delete(string $slug) {
        $post = $this->getPost(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        if (!$this->getUser() || ($this->getUser() != $post->getAuthor() && !$this->getUser()->isAdmin())):
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;

        $this->getEm()->remove($post);
        $this->getEm()->flush();

        return $this->redirectToRoute('index');
    }
}
