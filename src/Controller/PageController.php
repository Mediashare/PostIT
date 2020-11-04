<?php
namespace App\Controller;

use App\Entity\Page;
use App\Entity\Module;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController { 
    public function show(?Request $request, ?string $url, ?string $id) {
        $em = $this->getDoctrine()->getManager();
        // Get Page
        if ($url):
            $page = $em->getRepository(Page::class)->findOneBy(['url' => $url ? '/'.$url : $request->getRequestUri()], ['createDate' => 'DESC']);
        elseif ($id):
            $page = $em->getRepository(Page::class)->findOneBy(['id' => $id], ['createDate' => 'DESC']);
        endif;
        // If have not Page
        if (empty($page) || !$page->getMarkdown()):
            // Get last post
            return $this->redirectToRoute('index');
        endif;
        
        return $this->render('app/page.html.twig', ['content' => $page->getMarkdown() ?? '', 'page' => $page]);
    }
    
    public function form(Request $request, ?string $id = null) {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $id], ['createDate' => 'DESC']);
        if (!$page):
            $page = new Page();
            $page->setContent('');
            $page->setUrl('/');
            $page->setAuthor($this->getUser());
        endif;

        if ($request->isMethod('POST') && $request->get('content')):
            $page->setTitle($request->get('title'));
            $page->setDescription($request->get('description'));
            $page->setUrl($request->get('url') ?? '/');
            $page->setContent($request->get('content'));
            $page->setUpdateDate(new \DateTime());
            $em->persist($page);
            $em->flush();
            return $this->redirect($page->getUrl());
        endif;

        $modules = $em->getRepository(Module::class)->findAll();

        return $this->render('admin/page_form.html.twig', ['page' => $page, 'modules' => $modules]);
    }

    public function delete(string $id) {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findOneBy(['id' => $id]);
        if (!$page): return $this->redirectToRoute('admin'); endif;
        if (!$this->getUser() || ($this->getUser() != $page->getAuthor() && !$this->getUser()->isAdmin())):
            return $this->redirectToRoute('page', ['url' => $page->getUrl()]);
        endif;

        $em->remove($page);
        $em->flush();

        return $this->redirectToRoute('admin');
    }
}
