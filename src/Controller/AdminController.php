<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Edito;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin() {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findBy([], ['createDate' => 'DESC']);
        $comments = $em->getRepository(Comment::class)->findBy([], ['createDate' => 'DESC']);
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'posts' => $posts,
            'comments' => $comments,
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/edito", name="admin_edito")
     */
    public function edito(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $edito = $em->getRepository(Edito::class)->findOneBy([], ['createDate' => 'DESC']);
        if (!$edito):
            $edito = new Edito();
            $edito->setContent('');
            $edito->setAuthor($this->getUser());
            $em->persist($edito);
            $em->flush();
        endif;

        if ($request->isMethod('POST') && $request->get('content')):
            $edito->setContent($request->get('content'));
            $em->persist($edito);
            $em->flush();
        endif;

        return $this->render('admin/edito.html.twig', ['post' => $edito]);
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
