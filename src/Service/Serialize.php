<?php
namespace App\Service;

use App\Entity\User;

Class Serialize {
    public function posts($posts, ?string $type = 'json') {
        foreach ($posts ?? [] as $index => $post):
            if ($post && !is_array($post)):
                $posts[$index] = [
                    'id' => $post->getId(),
                    'title' => $post->getTitle(),
                    'slug' => $post->getSlug(),
                    'createDate' => $post->getCreateDate(),
                    'updateDate' => $post->getUpdateDate(),
                    'views' => $post->getViews(),
                    'messages' => count($post->getMessages()),
                    'author' => $this->author($post->getAuthor() ?? [], 'array'),
                ];
            elseif (is_array($post)):
                $posts[$index] = $post;
            endif;
        endforeach;

        if ($type === 'array'):
            return $posts ?? [];
        elseif ($type === 'json'):
            return \json_encode($posts);
        endif;
    }
    public function post($post, ?string $type = 'json') {
        if (!is_array($post)):    
            $post = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'slug' => $post->getSlug(),
                'article' => $post->getArticle()->getContent(),
                'html' => $post->getArticle()->getMarkdown(),
                'link' => $post->getLink()->getUrl(),
                'createDate' => $post->getCreateDate(),
                'updateDate' => $post->getUpdateDate(),
                'views' => $post->getViews(),
                'messages' => $this->messages($post->getMessages() ?? [], 'array'),
                'author' => $this->author($post->getAuthor() ?? [], 'array'),
            ];
        endif;
        if ($type === 'array'):
            return $post ?? [];
        elseif ($type === 'json'):
            return \json_encode($post);
        endif;
        return $post;
    }

    public function messages($messages, ?string $type = 'json') {
        foreach ($messages as $index => $message):
            $messages[$index] = $this->message($message, 'array');
        endforeach;
        if ($type === 'array'):
            return $messages;
        elseif ($type === 'json'):
            return json_encode($messages);
        endif;
    }

    public function message($message, ?string $type = 'json') {
        if (!is_array($message) && $type === 'array'):
            return [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'author' => $this->author($message->getAuthor() ?? [], 'array'),
                'createDate' => $message->getCreateDate()
            ];
        elseif ($type === 'json'):
            return json_encode($message);
        endif;
        return $message;
    }


    public function author($user, ?string $type = 'json') {
        if ($type === 'array'):
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'signature' => $user->getSignature(),
            ];
        elseif ($type === 'json'):
            return \json_encode($user);
        endif;
    }


    public function user(?User $user = null, ?string $type = 'json') {
        if (!$user): return []; endif;
        $user = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'slug' => $user->getSlug(),
            'avatar' => $user->getAvatar(),
            'signature' => $user->getSignature(),
            'posts' => $this->posts($user->getPosts() ?? [], 'array'),
            'messages' => $this->messages($user->getMessages() ?? [], 'array')
        ];
        if ($type === 'array'):
            return $user;
        elseif ($type === 'json'):
            return \json_encode($user);
        endif;
    }


    public function users($users, ?string $type = 'json') {
        foreach ($users as $index => $user):
            $users[$index] = $this->author($user, 'array');
        endforeach;
        if ($type === 'array'):
            return $users;
        elseif ($type === 'json'):
            return \json_encode($users);
        endif;
    }

    public function arrayToObject(array $array, ?string $class_name = null) {
        $entities = ['Page', 'Post', 'message', 'User'];
        foreach ([$class_name] ?? $entities as $class_name):
            $serialized = unserialize(sprintf('O:%d:"%s"%s', strlen('App\Entity\\' . $class_name), 'App\Entity\\' . $class_name, strstr(serialize($serialized ?? $array), ':')));
        endforeach;
        return $serialized;
    }
}