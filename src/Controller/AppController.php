<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Edito;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index() {
        $em = $this->getDoctrine()->getManager();
        // Get Edito
        $post = $em->getRepository(Edito::class)->findOneBy([], ['createDate' => 'DESC']);
        // If have not Edito
        if (!$post || !$post->getMarkdown()):
            // Get last post
            $post = $em->getRepository(Post::class)->findOneBy([], ['createDate' => 'DESC']);
        endif;
        // Get content
        if ($post): $content = $post->getMarkdown(); endif;
        if (empty($content)): $content = ''; endif;

        return $this->render('app/index.html.twig', ['content' => $content ?? '']);
    }

    public function menu(Request $request, ?Post $post) {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findBy([], ['createDate' => 'DESC']);
        return $this->render('_menu.html.twig', [
            'posts' => $posts,
            'currentPost' => $post,
            'request' => $request
        ]);
    }

    public function pageNotFound(Request $request) {
        $app_env = $this->getParameter('appenv');
        if ($app_env === "dev"):
            dd($request->attributes->get('exception'));
        else: return $this->redirectToRoute('index'); endif;
    }
}
