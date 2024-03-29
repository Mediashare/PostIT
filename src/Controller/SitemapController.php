<?php
namespace App\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SitemapController extends AbstractController {
    public function sitemap(Request $request) {
        $urls[$this->generateUrl('index')] = ['url' => $this->generateUrl('index', [], false), 'lastmod' => new \DateTime(), 'changefreq' => 'monthly', 'priority' => 1];
        $urls[$this->generateUrl('account')] = ['url' => $this->generateUrl('account', [], false), 'lastmod' => new \DateTime(), 'changefreq' => 'monthly', 'priority' => 1];
        
        foreach ($this->getPages([], ['updateDate' => 'DESC']) as $page):
            $urls[$page->getUrl()] = [
                'url' => $request->isSecure() ? 
                    'https://' . $request->headers->get('host').$page->getUrl() : 
                    'http://' . $request->headers->get('host').$page->getUrl(), 
                'lastmod' => $page->getUpdateDate(), 
                'changefreq' => 'monthly', 
                'priority' => 0.9
            ];
        endforeach;
        
        foreach ($this->getPosts(['online' => true], ['updateDate' => 'DESC']) as $post):
            $urls[$this->generateUrl('post', ['slug' => $post->getSlug()])] = [
                'url' => $this->generateUrl('post', ['slug' => $post->getSlug()], false), 
                'lastmod' => $post->getUpdateDate(), 
                'changefreq' => 'monthly', 
                'priority' => 0.8
            ];
        endforeach;
        
        return $this->render('app/sitemap.xml.twig', [
            'urls' => $urls ?? [],
        ]);
    }
}
