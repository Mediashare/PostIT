<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController {
    public function dashboard() {
        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository(Page::class)->findBy([], ['createDate' => 'DESC']);
        $posts = $em->getRepository(Post::class)->findBy([], ['createDate' => 'DESC']);
        $comments = $em->getRepository(Comment::class)->findBy([], ['createDate' => 'DESC']);
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/dashboard.html.twig', [
            'posts' => $posts,
            'comments' => $comments,
            'users' => $users,
            'pages' => $pages
        ]);
    }
}
