<?php

namespace App\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

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

    public function cookies(?Request $request, Session $session) {
        if ($session->get('allow_cookie') !== true):
            $cookie = true;
            if ($request->getPathInfo() === '/cookies'):
                $session->set('allow_cookie', true);
                return $this->redirectToRoute('app_index');
            endif;
        else: $cookie = false; endif;
        return $this->render('partial/_cookies.html.twig', ['cookie' => $cookie]);
    }

    public function pageNotFound(Request $request) {
        $app_env = $this->getParameter('appenv');
        if ($app_env === "dev"):
            dd($request->attributes->get('exception'));
        else: return $this->redirectToRoute('index'); endif;
    }
}
