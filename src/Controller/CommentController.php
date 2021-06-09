<?php
namespace App\Controller;

use App\Entity\Comment;
use Symfony\Component\Mime\Address;
use App\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;


class CommentController extends AbstractController {
    public function new(Request $request, string $slug, MailerInterface $mailer) {
        $post = $this->getPost(['slug' => $slug, 'online' => true]);
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
        
        // Email to author
        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('mailer.from'), $this->getParameter('mailer.from_name')))
            ->to(new Address($post->getAuthor()->getEmail(), $post->getAuthor()->getUsername()))
            ->subject('New comment for ' . $post->getTitle())
            // path of the Twig template to render
            ->htmlTemplate('mail/comment.html.twig')
            // pass variables (name => value) to the template
            ->context([
                'user' => $this->getUser(),
                'author' => $post->getAuthor(),
                'post' => $post,
                'comment' => $comment
            ]);
        $mailer->send($email);

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
