<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;

class AbstractController extends Controller {
    public function getFileContent(UploadedFile $file): ?string {
        return file_get_contents($file->getPathname());
    }

    public function getEm() {
        return $this->getDoctrine()->getManager();
    }

    public function getRepository(string $entity) {
        return $this->getEm()->getRepository($entity);
    }

    public function getPage(array $parameters, ?array $order = ['createDate' => 'DESC']) {
        return $this->getRepository(Page::class)->findOneBy($parameters, $order);
    }

    public function getPages(?array $parameters = [], ?array $order = ['createDate' => 'DESC']) {
        return $this->getRepository(Page::class)->findBy($parameters, $order);
    }

    public function getPost(array $parameters, ?array $order = ['createDate' => 'DESC']) {
        return $this->getRepository(Post::class)->findOneBy($parameters, $order);
    }

    public function getPosts(?array $parameters = [], ?array $order = ['createDate' => 'DESC']) {
        return $this->getRepository(Post::class)->findBy($parameters, $order);
    }

    public function getComment(array $parameters, ?array $order = ['createDate' => 'DESC']) {
        return $this->getRepository(Comment::class)->findOneBy($parameters, $order);
    }

    public function getComments(?array $parameters = [], ?array $order = ['createDate' => 'DESC']) {
        return $this->getRepository(Comment::class)->findBy($parameters, $order);
    }

    public function getUser(?array $parameters = null, ?array $order = []) {
        if (!$parameters): return Controller::getUser(); endif;
        return $this->getRepository(User::class)->findOneBy($parameters, $order);
    }

    public function getUsers(?array $parameters = [], ?array $order = []) {
        return $this->getRepository(User::class)->findBy($parameters, $order);
    }
}
