<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Service\Serialize;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/posts", name="api_posts")
     */
    public function posts(): Response {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findBy([], ['createDate' => 'desc']);
        $serizalizer = new Serialize();
        $posts = $serizalizer->posts($posts, $type = 'array');
        
        return $this->json(['status' => 'success', 'posts' => $posts ?? []]);
    }
    /**
     * @Route("/api/post/{id}", name="api_post")
     */
    public function post(string $id): Response {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        $serizalizer = new Serialize();
        $post = $serizalizer->post($post, $type = 'array');
        
        return $this->json(['status' => 'success', 'post' => $post ?? []]);
    }

    /**
     * @Route("/api/users", name="api_users")
     */
    public function users(): Response {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $serizalizer = new Serialize();
        $users = $serizalizer->users($users, $type = 'array');
        
        return $this->json(['status' => 'success', 'users' => $users ?? []]);
    }

    /**
     * @Route("/api/user/{id}", name="api_user")
     */
    public function user(string $id): Response {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $serizalizer = new Serialize();
        $author = $serizalizer->author($user, $type = 'array');
        $posts = $serizalizer->posts($user->getPosts() ?? [], $type = 'array');
        $comments = $serizalizer->comments($user->getComments() ?? [], $type = 'array');
        
        return $this->json(['status' => 'success', 'user' => ['profile' => $author, 'posts' => $posts, 'comments' => $comments]]);
    }
}
