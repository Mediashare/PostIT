<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Page;
use App\Entity\Comment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin() {
        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository(Page::class)->findBy([], ['createDate' => 'DESC']);
        $posts = $em->getRepository(Post::class)->findBy([], ['createDate' => 'DESC']);
        $comments = $em->getRepository(Comment::class)->findBy([], ['createDate' => 'DESC']);
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'posts' => $posts,
            'comments' => $comments,
            'users' => $users,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_user_delete")
     */
    public function userDelete(string $id) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user):
            $this->addFlash('error', 'No user found.');
            return $this->redirectToRoute('admin');
        endif;
        foreach ($user->getPosts() as $post):
            $em->remove($post);
        endforeach;
        foreach ($user->getComments() as $comment):
            $em->remove($comment);
        endforeach;
        
        $em->remove($user);
        $em->flush();
        
        return $this->redirectToRoute('admin');
    }
}
