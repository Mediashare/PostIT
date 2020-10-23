<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index() {
        return $this->render('app/index.html.twig');
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
}
