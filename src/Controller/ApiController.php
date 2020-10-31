<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Service\Text;
use App\Service\Serialize;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController {
    public function markdownify(Request $request) {
        $text = new Text();
        $markdown = $text->markdownify($request->get('content') ?? '');
        return new Response($markdown, 200);
    }
    
    public function posts(): Response {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findBy([], ['createDate' => 'desc']);
        $serizalizer = new Serialize();
        $posts = $serizalizer->posts($posts, $type = 'array');
        
        return $this->json(['status' => 'success', 'posts' => $posts ?? []]);
    }

    public function post(string $id): Response {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        $serizalizer = new Serialize();
        $post = $serizalizer->post($post, $type = 'array');
        
        return $this->json(['status' => 'success', 'post' => $post ?? []]);
    }

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
