<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Post;
use App\Service\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController {
    public function index(Request $request) {
        $em = $this->getDoctrine()->getManager();
        // Get Page
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['url' => '/'], ['createDate' => 'DESC']);
        if ($page && $page->getMarkdown()): 
            $content = $page->getMarkdown();
            return $view->page($page);
        else:
            // If have not Page
            $post = $em->getRepository(Post::class)->findOneBy([], ['createDate' => 'DESC']); // Get last post
            $content = $post->getMarkdown();
            return $view->post($post);
        endif;
    }

    public function menu(Request $request, ?Post $post, ?Page $page) {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findBy([], ['createDate' => 'DESC']);
        return $this->render('_menu.html.twig', [
            'posts' => $posts,
            'currentPost' => $post,
            'page' => $page,
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
