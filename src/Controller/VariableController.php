<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Variable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VariableController extends AbstractController {    
    public function delete(string $id): Response {
        $em = $this->getDoctrine()->getManager();
        $variable = $em->getRepository(Variable::class)->find($id);
        if (!$variable):
            $this->addFlash('error', 'Input not found!');
            return $this->redirectToRoute('admin');
        else: 
            $module = $em->getRepository(Module::class)->find($variable->getModule());
            if (!$module):
                $this->addFlash('error', 'Module not found!');
                return $this->redirectToRoute('admin');
            endif; 
            $module->removeVariable($variable);
            $em->persist($module);
            $em->remove($variable);
            $em->flush();
        endif;

        return $this->redirectToRoute('module_form', ['id' => $module->getId()]);
    }
}
