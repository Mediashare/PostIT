<?php
namespace App\Controller;

use App\Entity\Page;
use App\Entity\Post;
use App\Entity\User;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PartialController extends AbstractController {
    public function menu(Request $request, ?Post $post, ?Page $page, ?User $user) {
        $posts = $this->getPosts([], ['updateDate' => 'DESC']);
        return $this->render('partial/_menu.html.twig', [
            'request' => $request,
            'currentPost' => $post,
            'page' => $page,
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function signature(?string $username = null) {
        $user = $this->getUser(['username' => $username]);
        if (!$user):
            if (!$this->getUser()): return new Response(''); endif;
            $user = $this->getUser();
        endif;
        return $this->render('partial/_signature.html.twig', ['user' => $user]);
    }
}