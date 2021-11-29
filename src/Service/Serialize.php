<?php
namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;

Class Serialize {
    public function posts($posts, ?string $type = 'json') {
        foreach ($posts ?? [] as $index => $post):
            if ($post && !is_array($post)):
                $posts[$index] = [
                    'id' => $post->getId(),
                    'title' => $post->getTitle(),
                    'slug' => $post->getSlug(),
                    'createDate' => $post->getCreateDate(),
                    'comments' => count($post->getComments()),
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
                'content' => $post->getContent(),
                'html' => $post->getMarkdown(),
                'createDate' => $post->getCreateDate(),
                'comments' => $this->comments($post->getComments() ?? [], 'array'),
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

    public function comments($comments, ?string $type = 'json') {
        foreach ($comments as $index => $comment):
            $comments[$index] = $this->comment($comment, 'array');
        endforeach;
        if ($type === 'array'):
            return $comments;
        elseif ($type === 'json'):
            return json_encode($comments);
        endif;
    }

    public function comment($comment, ?string $type = 'json') {
        if (!is_array($comment) && $type === 'array'):
            return [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'author' => $this->author($comment->getAuthor() ?? [], 'array'),
                'createDate' => $comment->getCreateDate()
            ];
        elseif ($type === 'json'):
            return json_encode($comment);
        endif;
        return $comment;
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
            'comments' => $this->comments($user->getComments() ?? [], 'array')
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
        $entities = ['Page', 'Post', 'Comment', 'User'];
        foreach ([$class_name] ?? $entities as $class_name):
            $serialized = unserialize(sprintf('O:%d:"%s"%s', strlen('App\Entity\\' . $class_name), 'App\Entity\\' . $class_name, strstr(serialize($serialized ?? $array), ':')));
        endforeach;
        return $serialized;
    }
}