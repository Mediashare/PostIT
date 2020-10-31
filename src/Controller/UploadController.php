<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadController extends AbstractController {
    public function upload(Request $request) {
        $title = $request->get('title');
        $content = $request->get('content') ?? $this->getFileContent($request->files->get('content'));
        if ($title && $content):
            $em = $this->getDoctrine()->getManager();
            $post = new Post();
            $post->setTitle($title);
            $post->setContent($content);
            // Generate Slug
            $post->setSlug($post->getTitle());
            while ($duplication = $em->getRepository(Post::class)->findOneBy(['slug' => $post->getSlug()])):
                if (!isset($slug_counter)): $slug_counter = 0; endif;
                $slug_counter++;
                $post->setSlug($post->getTitle().'-'.$slug_counter);
            endwhile;
            // Check Apikey / Author
            if ($author = $em->getRepository(User::class)->findOneBy(['apikey' => $request->headers->get('ApiKey')])):
                $post->setAuthor($author);
            endif;
            $em->persist($post);
            $em->flush();
            return $this->json(['status' => 'success', 'url' => $this->generateUrl('post', ['slug' => $post->getSlug()], false)]);
        endif;
        return $this->json(['status' => 'error']);
    }

    private function getFileContent(UploadedFile $file): ?string {
        return file_get_contents($file->getPathname());
    }
}
