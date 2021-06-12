<?php

namespace App\Controller;

use App\Controller\AbstractController;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController {
    public function index(Request $request) {
        // Get Page
        $page = $this->getPage(['url' => '/'], ['createDate' => 'DESC']);
        if ($page && $page->getMarkdown()): 
            return $this->render('app/page.html.twig', ['page' => $page]);
        elseif ($post = $this->getPost(['online' => true], ['createDate' => 'DESC'])):
            // If have not Page
            // Get last post
            return $this->render('app/post.html.twig', ['post' => $post]);
        else:
            return $this->redirectToRoute('account');
        endif;
    }

    public function pageNotFound(Request $request) {
        $app_env = $this->getParameter('appenv');
        if ($app_env === "dev"):
            dd($request->attributes->get('exception'));
        else: return $this->redirectToRoute('index'); endif;
    }
}
