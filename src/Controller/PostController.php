<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            $post->setTitle($request->get('title'));
            $post->setContent($request->get('content'));
            // Generate Slug
            $post->setSlug($post->getTitle());
            while ($duplication = $em->getRepository(Post::class)->findOneBy(['slug' => $post->getSlug()])):
                if (!isset($slug_counter)): $slug_counter = 0; endif;
                $slug_counter++;
                $post->setSlug($post->getTitle().'-'.$slug_counter);
            endwhile;

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;
        
        return $this->render('post/form.html.twig', ['post' => $post]);
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
