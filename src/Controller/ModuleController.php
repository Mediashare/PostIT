<?php
namespace App\Controller;

use App\Entity\Module;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ModuleController extends AbstractController { 
    public function form(Request $request, ?string $id = null) {
        $module = $this->getModule(['id' => $id], ['createDate' => 'DESC']);
        if (!$module):
            $module = new Module();
            $module->setContent('');
            $module->setName('module_name');
        endif;

        if ($request->isMethod('POST') && $request->get('content')):
            $module->setName($request->get('name'));
            $module->setContent($request->get('content'));
            $module->setUpdateDate(new \DateTime());
            $this->getEm()->persist($module);
            $this->getEm()->flush();
            return $this->redirectToRoute('admin');
        endif;

        return $this->render('admin/module.html.twig', ['page' => $module]);
    }

    public function delete(string $id) {
        $module = $this->getModule(['id' => $id]);
        if (!$module): return $this->redirectToRoute('admin'); endif;
        if (!$this->getUser() || !$this->getUser()->isAdmin()):
            return $this->redirectToRoute('app');
        endif;

        $this->getEm()->remove($module);
        $this->getEm()->flush();

        return $this->redirectToRoute('admin');
    }
}
