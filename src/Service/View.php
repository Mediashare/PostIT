<?php

namespace App\Service;

use App\Entity\Page;
use App\Entity\Post;
use Twig\Environment as Twig;
use Symfony\Component\HttpFoundation\Response;

Class View {
    private $twig;
    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }
    public function page(Page $page): ?Response {
        return new Response($this->twig->render('page/show.html.twig', ['page' => $page]), 200);
    }
    public function post(Post $post): ?Response {
        return new Response($this->twig->render('post/show.html.twig', ['post' => $post]), 200);
    }
    public function pageForm(?Page $page): ?Response {
        return new Response($this->twig->render('page/form.html.twig', ['page' => $page ?? null]), 200);
    }
    public function postForm(?Post $post): ?Response {
        return new Response($this->twig->render('post/form.html.twig', ['post' => $post ?? null]), 200);
    }
}
