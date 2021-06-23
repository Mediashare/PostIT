<?php
namespace App\Controller;

use App\Entity\Page;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController { 
    public function show(?Request $request, ?string $url, ?string $id) {
        // Get Page
        if ($url):
            $page = $this->getPage(['url' => $url ? '/'.$url : $request->getRequestUri()], ['createDate' => 'DESC']);
        elseif ($id):
            $page = $this->getPage(['id' => $id], ['createDate' => 'DESC']);
        endif;
        // If have not Page
        if (empty($page) || !$page->getMarkdown()):
            // Get last post
            return $this->redirectToRoute('index');
        endif;
        
        return $this->render('app/page.html.twig', ['content' => $page->getMarkdown() ?? '', 'page' => $page]);
    }
    
    public function form(Request $request, ?string $id = null) {
        $page = $this->getPage(['id' => $id], ['createDate' => 'DESC']);
        if (!$page):
            $page = new Page();
            $page->setContent('');
            $page->setUrl('/');
            // $page->setAuthor($this->getUser());
        endif;

        if ($request->isMethod('POST') && $request->get('content')):
            $page->setTitle($request->get('title'));
            $page->setDescription($request->get('description'));
            $page->setUrl($request->get('url') ?? '/');
            $page->setContent($request->get('content'));
            $page->setUpdateDate(new \DateTime());
            $this->getEm()->persist($page);
            $this->getEm()->flush();
            return $this->redirect($page->getUrl());
        endif;

        return $this->render('admin/page.html.twig', ['page' => $page]);
    }

    public function delete(string $id) {
        $page = $this->getPage(['id' => $id]);
        if (!$page): return $this->redirectToRoute('admin'); endif;
        if (!$this->getUser() || !$this->getUser()->isAdmin()):
            return $this->redirectToRoute('page', ['url' => $page->getUrl()]);
        endif;

        $this->getEm()->remove($page);
        $this->getEm()->flush();

        return $this->redirectToRoute('admin');
    }
}
