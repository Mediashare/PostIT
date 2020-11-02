<?php

namespace App\Controller;

use App\Entity\Input;
use App\Entity\Module;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InputController extends AbstractController {
    public function delete(int $module, int $id): Response {
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
