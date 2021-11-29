<?php

namespace App\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController {
    public function dashboard() {
        $pages = $this->getPages([], ['updateDate' => 'DESC']);
        $templates = $this->getTemplates([], ['updateDate' => 'DESC']);
        $posts = $this->getPosts([], ['updateDate' => 'DESC']);
        $comments = $this->getComments([], ['createDate' => 'DESC']);
        $users = $this->getUsers();
        return $this->render('admin/dashboard.html.twig', [
            'pages' => $pages,
            'templates' => $templates,
            'posts' => $posts,
            'comments' => $comments,
            'users' => $users,
        ]);
    }
}
