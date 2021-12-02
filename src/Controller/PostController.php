<?php

namespace App\Controller;

use App\Entity\Post;
use App\Controller\AbstractController;
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

    public function form(Request $request, ?string $slug = null) {
        if ($slug):
            $post = $this->getPost(['slug' => $slug], ['updateDate' => 'DESC']);
            if (!$post):
                return $this->redirectToRoute('post_new');
            elseif (!$this->getUser() || ($this->getUser() != $post->getAuthor() && !$this->getUser()->isAdmin())):
                return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
            endif;
        else: 
            $post = new Post(); 
            if ($this->getUser()): $post->setAuthor($this->getUser()); endif;
        endif;

        if ($request->isMethod('POST') && $request->get('title') && $request->get('content')):
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
            $post->setContent($request->get('content'));
            $post->setOnline($request->get('online') ? true : false);
            $post->setUpdateDate(new \DateTime());
            $this->getEm()->persist($post);
            $this->getEm()->flush();
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;
        
        return $this->render('app/post_form.html.twig', ['post' => $post]);
    }

    public function upload(Request $request) {
        $user = $this->getUser(['apikey' => $request->headers->get('ApiKey')]);
        if ($user):
            $title = $request->get('title');
            $content = $request->get('content') ?? $this->getFileContent($request->files->get('content'));
            if ($title && $content):
                $post = new Post();
                $post->setTitle($title);
                $post->setContent($content);
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
