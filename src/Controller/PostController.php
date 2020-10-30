<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/post/{slug}/", name="post")
     */
    public function show(string $slug) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
    
    /**
     * @Route("/new", name="post_new")
     */
    public function new(Request $request) {
        if ($request->isMethod('POST') && $request->get('title') && $request->get('content')):
            $em = $this->getDoctrine()->getManager();
            $post = new Post();
            $post->setTitle($request->get('title'));
            $post->setContent($request->get('content'));
            // Generate Slug
            $post->setSlug($post->getTitle());
            while ($duplication = $em->getRepository(Post::class)->findOneBy(['slug' => $post->getSlug()])):
                if (!isset($slug_counter)): $slug_counter = 0; endif;
                $slug_counter++;
                $post->setSlug($post->getTitle().'-'.$slug_counter);
            endwhile;
            if ($this->getUser()):
                $post->setAuthor($this->getUser());
            endif;
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;
        return $this->render('post/form.html.twig');
    }

    /**
     * @Route("/edit/{slug}/", name="post_edit")
     */
    public function edit(Request $request, string $slug) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        if (!$this->getUser() || ($this->getUser() != $post->getAuthor() && !$this->getUser()->isAdmin())):
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;

        if ($request->isMethod('POST') && $request->get('title') && $request->get('content')):
            $post->setTitle($request->get('title'));
            $post->setContent($request->get('content'));
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

        return $this->render('post/form.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/delete/{slug}/", name="post_delete")
     */
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
