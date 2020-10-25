<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/{slug}", name="comment")
     */
    public function comment(Request $request, string $slug) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
        if (!$post): 
            $this->addFlash('error', 'Post not found.');
            return $this->redirectToRoute('index'); 
        endif;

        if (!$this->getUser()):
            $this->addFlash('error', 'You must login to send a comment.');
            return $this->redirectToRoute('index'); 
        endif;

        if ($request->isMethod('POST')):
            $comment = new Comment();
            $comment->setContent($request->get('comment'));
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $em->persist($comment);
            $em->flush();
        endif;
        
        return $this->redirectToRoute('post', ['slug' => $post->getSlug(), '_fragment' => 'comment_'.$comment->getId() ?? '']);
    }

    /**
     * @Route("/comment/{slug}/delete/{id}", name="comment_delete")
     */
    public function delete(Request $request, string $slug, string $id) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        $comment = $em->getRepository(Comment::class)->findOneBy(['post' => $post, 'id' => $id]);
        if (!$comment): return $this->redirectToRoute('post', ['slug' => $post->getSlug()]); endif;
        if (!$this->getUser() || ($this->getUser() != $comment->getAuthor() && !$this->getUser()->isAdmin())):
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;

        $post->removeComment($comment);
        $em->remove($comment);
        $em->persist($post);
        $em->flush();
        return $this->redirectToRoute('post', ['slug' => $post->getSlug(), '_fragment' => 'last_comment']);
    }
}
