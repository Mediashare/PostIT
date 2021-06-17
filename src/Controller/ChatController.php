<?php
namespace App\Controller;

use App\Controller\AbstractController;
use App\Entity\ChatMessage;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends AbstractController { 
    public function show() {
        return $this->render('app/chat.html.twig', ['messages' => $this->getChatMessages()]);
    }

    public function messages() {
        foreach ($this->getChatMessages() ?? [] as $message):
            $messages[] = [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'createDate' => $message->getCreateDate(),
                'author' => [
                    'username' => $message->getAuthor()->getUsername(),
                    'profile' => $this->generateUrl('profile', ['username' => $message->getAuthor()->getSlug()]),
                    'avatar' => $message->getAuthor()->getAvatar(),
                ]
            ];
        endforeach;

        return $this->json([
            'status' => 'success',
            'messages' => $messages ?? []
        ]);
    }

    public function form(Request $request) {
        if ($request->get('message') && $this->getUser()):
            $message = new ChatMessage();
            $message->setContent($request->get('message'));
            $message->setAuthor($this->getUser());
            $message->setCreateDate(new \DateTime());
            $this->getEm()->persist($message);
            $this->getEm()->flush();

            return $this->json([
                'status' => 'success',
                'message' => [
                    'id' => $message->getId(),
                    'content' => $message->getContent(),
                    'createDate' => $message->getCreateDate(),
                    'author' => [
                        'username' => $message->getAuthor()->getUsername(),
                        'profile' => $this->generateUrl('profile', ['username' => $message->getAuthor()->getSlug()]),
                        'avatar' => $message->getAuthor()->getAvatar(),
                    ]
                ],
            ]);
        endif;
        return $this->json([
            'status' => 'error'
        ]);
    }
}
