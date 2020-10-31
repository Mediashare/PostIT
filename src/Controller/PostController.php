<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController {    
    public function show(string $slug) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        return $this->render('post/show.html.twig', ['post' => $post]);
    }

    public function form(Request $request, ?string $slug = null) {
        $em = $this->getDoctrine()->getManager();
        if ($slug):
            $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug], ['createDate' => 'DESC']);
            if (!$this->getUser() || ($this->getUser() != $post->getAuthor() && !$this->getUser()->isAdmin())):
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
                while ($duplication = $em->getRepository(Post::class)->findOneBy(['slug' => $post->getSlug()])):
                    if (!isset($slug_counter)): $slug_counter = 0; endif;
                    $slug_counter++;
                    $post->setSlug($post->getTitle().'-'.$slug_counter);
                endwhile;
            endif;
            $post->setContent($request->get('content'));
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;
        
        return $this->render('post/form.html.twig', ['post' => $post]);
    }

    public function upload(Request $request) {
        $title = $request->get('title');
        $content = $request->get('content') ?? $this->getFileContent($request->files->get('content'));
        if ($title && $content):
            $em = $this->getDoctrine()->getManager();
            $post = new Post();
            $post->setTitle($title);
            $post->setContent($content);
            // Generate Slug
            $post->setSlug($post->getTitle());
            while ($duplication = $em->getRepository(Post::class)->findOneBy(['slug' => $post->getSlug()])):
                if (!isset($slug_counter)): $slug_counter = 0; endif;
                $slug_counter++;
                $post->setSlug($post->getTitle().'-'.$slug_counter);
            endwhile;
            // Check Apikey / Author
            if ($author = $em->getRepository(User::class)->findOneBy(['apikey' => $request->headers->get('ApiKey')])):
                $post->setAuthor($author);
            endif;
            $em->persist($post);
            $em->flush();
            return $this->json(['status' => 'success', 'url' => $this->generateUrl('post', ['slug' => $post->getSlug()], false)]);
        endif;
        return $this->json(['status' => 'error']);
    }

    public function delete(string $slug) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        if (!$this->getUser() || ($this->getUser() != $post->getAuthor() && !$this->getUser()->isAdmin())):
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;

        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('index');
    }
}
