<?php
namespace App\Controller;

use App\Entity\Message;
use Symfony\Component\Mime\Address;
use App\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;


class MessageController extends AbstractController {
    public function new(Request $request, string $slug, MailerInterface $mailer) {
        $post = $this->getPost(['slug' => $slug, 'online' => true]);
        if (!$post): 
            $this->addFlash('error', 'Post not found.');
            return $this->redirectToRoute('index'); 
        endif;

        if (!$this->getUser()):
            $this->addFlash('error', 'You must login to send a message.');
            return $this->redirectToRoute('index'); 
        endif;

        if ($request->isMethod('POST')):
            $message = new Message();
            $message->setContent($request->get('message'));
            $message->setPost($post);
            $message->setAuthor($this->getUser());
            $this->getEm()->persist($message);
            $this->getEm()->flush();
        endif;
        
        // Email to author
        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('mailer.from'), $this->getParameter('mailer.from_name')))
            ->to(new Address($post->getAuthor()->getEmail(), $post->getAuthor()->getUsername()))
            ->subject('Nouveau message')
            // path of the Twig template to render
            ->htmlTemplate('mail/message.html.twig')
            // pass variables (name => value) to the template
            ->context([
                'user' => $this->getUser(),
                'author' => $post->getAuthor(),
                'post' => $post,
                'message' => $message
            ]);
        $mailer->send($email);

        return $this->redirectToRoute('post', ['slug' => $post->getSlug(), '_fragment' => 'message_'.$message->getId() ?? '']);
    }

    public function delete(string $slug, string $id) {
        $post = $this->getPost(['slug' => $slug]);
        if (!$post): return $this->redirectToRoute('index'); endif;
        $message = $this->getMessage(['post' => $post, 'id' => $id]);
        if (!$message): return $this->redirectToRoute('post', ['slug' => $post->getSlug()]); endif;
        if (!$this->getUser() || ($this->getUser() != $message->getAuthor() && !$this->getUser()->isAdmin())):
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        endif;

        $post->removemessage($message);
        $this->getEm()->remove($message);
        $this->getEm()->persist($post);
        $this->getEm()->flush();

        return $this->redirectToRoute('post', ['slug' => $post->getSlug(), '_fragment' => 'last_message']);
    }
}
