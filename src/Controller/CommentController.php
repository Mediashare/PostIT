<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController {
    public function new(Request $request, string $slug) {
        $post = $this->getPost(['slug' => $slug]);
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
            $this->getEm()->persist($comment);
            $this->getEm()->flush();
        endif;
        
        return $this->redirectToRoute('post', ['slug' => $post->getSlug(), '_fragment' => 'comment_'.$comment->getId() ?? '']);
    }

    public function delete(Request $request, string $slug, string $id) {
        $post = $this->getPost(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        $comment = $this->getComment(['post' => $post, 'id' => $id]);
        if (!$comment): return $this->redirectToRoute('post', ['slug' => $post->getSlug()]); endif;
        if (!$this->getUser() || ($this->getUser() != $comment->getAuthor() && !$this->getUser()->isAdmin())):
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;

        $post->removeComment($comment);
        $this->getEm()->remove($comment);
        $this->getEm()->persist($post);
        $this->getEm()->flush();

        return $this->redirectToRoute('post', ['slug' => $post->getSlug(), '_fragment' => 'last_comment']);
    }
}
