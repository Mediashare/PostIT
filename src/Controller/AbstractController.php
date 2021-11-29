<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;

class AbstractController extends Controller {
    public function getFileContent(UploadedFile $file): ?string {
        return file_get_contents($file->getPathname());
    }

    public function getEm(): EntityManagerInterface {
        return $this->getDoctrine()->getManager();
    }

    public function getRepository(string $entity): ObjectRepository {
        return $this->getEm()->getRepository($entity);
    }

    public function getPage(array $parameters, ?array $order = ['updateDate' => 'DESC']): ?Page {
        return $this->getRepository(Page::class)->findOneBy($parameters, $order);
    }

    public function getPages(?array $parameters = [], ?array $order = ['updateDate' => 'DESC']): ?array {
        return $this->getRepository(Page::class)->findBy($parameters, $order);
    }

    public function getPost(array $parameters, ?array $order = ['updateDate' => 'DESC']): ?Post {
        return $this->getRepository(Post::class)->findOneBy($parameters, $order);
    }

    public function getPosts(?array $parameters = [], ?array $order = ['updateDate' => 'DESC']): ?array {
        return $this->getRepository(Post::class)->findBy($parameters, $order);
    }

    public function getComment(array $parameters, ?array $order = ['createDate' => 'DESC']): ?Comment {
        return $this->getRepository(Comment::class)->findOneBy($parameters, $order);
    }

    public function getComments(?array $parameters = [], ?array $order = ['createDate' => 'DESC']): ?array {
        return $this->getRepository(Comment::class)->findBy($parameters, $order);
    }

    public function getUser(?array $parameters = null, ?array $order = []): ?User {
        if (!$parameters): return Controller::getUser(); endif;
        return $this->getRepository(User::class)->findOneBy($parameters, $order);
    }

    public function getUsers(?array $parameters = [], ?array $order = []): ?array {
        return $this->getRepository(User::class)->findBy($parameters, $order);
    }

    public function getTemplate(array $parameters = [], ?array $order = []): ?Template {
        return $this->getRepository(Template::class)->findOneBy($parameters, $order);
    }

    public function getTemplates(?array $parameters = [], ?array $order = []): ?array {
        return $this->getRepository(Template::class)->findBy($parameters, $order);
    }
}
