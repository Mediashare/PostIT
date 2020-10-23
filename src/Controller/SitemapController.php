<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap", name="sitemap")
     */
    public function sitemap() {
        $urls[] = ['url' => $this->generateUrl('index', [], false), 'lastmod' => new \DateTime(), 'changefreq' => 'monthly', 'priority' => 1];
        $urls[] = ['url' => $this->generateUrl('post_new', [], false), 'lastmod' => new \DateTime(), 'changefreq' => 'monthly', 'priority' => 1];
        $urls[] = ['url' => $this->generateUrl('account', [], false), 'lastmod' => new \DateTime(), 'changefreq' => 'monthly', 'priority' => 1];
        
        $em = $this->getDoctrine()->getManager();
        foreach ($em->getRepository(Post::class)->findBy([], ['createDate' => 'DESC']) as $post):
            $urls[] = ['url' => $this->generateUrl('post', ['slug' => $post->getSlug()], false), 'lastmod' => $post->getCreateDate(), 'changefreq' => 'monthly', 'priority' => 0.9];
        endforeach;
        
        return $this->render('sitemap/index.xml.twig', [
            'urls' => $urls ?? [],
        ]);
    }
}
