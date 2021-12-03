<?php
namespace App\Controller;

use App\Entity\Page;
use App\Entity\Post;
use App\Entity\User;
use App\Controller\AbstractController;
use App\Service\Text;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PartialController extends AbstractController {
    public function menu(Request $request, ?Post $post, ?Page $page, ?User $user) {
        $posts = $this->getPosts([], ['createDate' => 'DESC']);
        return $this->render('partial/_menu.html.twig', [
            'request' => $request,
            'currentPost' => $post,
            'page' => $page,
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function og_image(Request $request, ?Post $post = null, ?User $user = null) {
        if ($post):
            $text = new Text();
            $crawler = new Crawler($text->markdownify($post->getContent()));
            if ($crawler->filter('img')->count() > 0):
                $image = $crawler->filter('img')->eq(0);
                $image = $image->attr('src');
            endif;
        elseif ($user && $user->getAvatar()):
            $image = $user->getAvatar();
            $image = $request->getUriForPath('/images/avatars/' . $image);
        endif;

        return $this->render('partial/_og_image.html.twig', ['image' => $image ?? null]);
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