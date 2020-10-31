<?php
namespace App\Controller;

use App\Entity\Page;
use App\Entity\Post;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SitemapController extends AbstractController {
    public function sitemap(Request $request) {
        $urls[$this->generateUrl('index')] = ['url' => $this->generateUrl('index', [], false), 'lastmod' => new \DateTime(), 'changefreq' => 'monthly', 'priority' => 1];
        $urls[$this->generateUrl('post_new')] = ['url' => $this->generateUrl('post_new', [], false), 'lastmod' => new \DateTime(), 'changefreq' => 'monthly', 'priority' => 1];
        $urls[$this->generateUrl('account')] = ['url' => $this->generateUrl('account', [], false), 'lastmod' => new \DateTime(), 'changefreq' => 'monthly', 'priority' => 1];
        
        $em = $this->getDoctrine()->getManager();
        foreach ($em->getRepository(Page::class)->findBy([], ['createDate' => 'DESC']) as $page):
            $urls[$page->getUrl()] = [
                'url' => $request->isSecure() ? 
                    'https://' . $request->headers->get('host').$page->getUrl() : 
                    'http://' . $request->headers->get('host').$page->getUrl(), 
                'lastmod' => $page->getCreateDate(), 
                'changefreq' => 'monthly', 
                'priority' => 0.9
            ];
        endforeach;
        
        foreach ($em->getRepository(Post::class)->findBy([], ['createDate' => 'DESC']) as $post):
            $urls[$this->generateUrl('post', ['slug' => $post->getSlug()])] = [
                'url' => $this->generateUrl('post', ['slug' => $post->getSlug()], false), 
                'lastmod' => $post->getCreateDate(), 
                'changefreq' => 'monthly', 
                'priority' => 0.8
            ];
        endforeach;
        
        return $this->render('sitemap/index.xml.twig', [
            'urls' => $urls ?? [],
        ]);
    }
}
