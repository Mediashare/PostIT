<?php

namespace App\Controller;

use App\Entity\Input;
use App\Entity\Module;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InputController extends AbstractController {
    public function form(Request $request, ?string $id = null): Response {
        $em = $this->getDoctrine()->getManager();
        $id = $id ?? $request->request->get('id');
        if ($id): $input = $em->getRepository(Input::class)->find($id); endif;
        if (empty($input)): 
            $input = new Input(); 
            if ($id): $input->setId($id); endif;
        endif;
        if ($request->isMethod('POST')):
            $input->setType($request->get('type'));
            $input->setRender($request->get('render_' . $input->getId()));
            $em->persist($input);
            $em->flush();
            return $this->redirectToRoute('admin');
        endif;
        return $this->render('admin/input.html.twig', ['input' => $input]);
    }
    
    public function delete(string $id): Response {
        $em = $this->getDoctrine()->getManager();
        $input = $em->getRepository(Input::class)->find($id);
        if (!$input):
            $this->addFlash('error', 'Input not found!');
            return $this->redirectToRoute('admin');
        else: 
            $module = $em->getRepository(Module::class)->find($module);
            if (!$module):
                $this->addFlash('error', 'Module not found!');
                return $this->redirectToRoute('admin');
            endif; 
            $module->removeInput($input);
            $em->persist($module);
            $em->remove($input);
            $em->flush();
        endif;

        return $this->redirectToRoute('module_form', ['id' => $module->getId()]);
    }
}
