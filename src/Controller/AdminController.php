<?php

namespace App\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController {
    public function dashboard() {
        $pages = $this->getPages([], ['createDate' => 'DESC']);
        $posts = $this->getPosts([], ['createDate' => 'DESC']);
        $comments = $this->getComments([], ['createDate' => 'DESC']);
        $users = $this->getUsers();
        return $this->render('admin/dashboard.html.twig', [
            'posts' => $posts,
            'comments' => $comments,
            'users' => $users,
            'pages' => $pages
        ]);
    }
}
