<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

trait View {
    private $twig;
    public function __construct(\Twig_Environment $twig) {
        $this->twig = $twig;
    }
    public function page($content): ?Response {
        return new Response($this->twig->render('page/show.html.twig', ['content' => $content]));
    }
    public function post($content): ?Response {
        return new Response('post/show.html.twig', ['content' => $content]);
    }
}
