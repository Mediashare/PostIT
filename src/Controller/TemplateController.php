<?php
namespace App\Controller;

use App\Entity\Template;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TemplateController extends AbstractController { 
    public function form(Request $request, ?string $id = null) {
        $template = $this->getTemplate(['id' => $id, 'user' => $this->getUser()], ['createDate' => 'DESC']);
        if (!$template):
            $template = new Template();
            $template->setContent('');
            $template->setName('template_name');
            $template->setUser($this->getUser());
        endif;

        if ($request->isMethod('POST') && $request->get('content')):
            $template->setName($request->get('name'));
            $template->setContent($request->get('content'));
            $template->setUpdateDate(new \DateTime());
            $template->setUser($this->getUser());
            $this->getEm()->persist($template);
            $this->getEm()->flush();
            return $this->redirectToRoute('profile');
        endif;

        return $this->render('app/template.html.twig', ['template' => $template]);
    }

    public function delete(string $id) {
        $template = $this->getTemplate(['id' => $id, 'user' => $this->getUser()]);
        if (!$template): return $this->redirectToRoute('profile'); endif;
        if (!$this->getUser() || !$this->getUser()->isAdmin()):
            return $this->redirectToRoute('app');
        endif;

        $this->getEm()->remove($template);
        $this->getEm()->flush();

        return $this->redirectToRoute('profile');
    }
}
