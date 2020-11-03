<?php

namespace App\Controller;

use App\Entity\Input;
use App\Entity\Module;
use App\Entity\Variable;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleController extends AbstractController {
    public function form(Request $request, ?string $id = null): Response {
        $em = $this->getDoctrine()->getManager();
        if ($id): $module = $em->getRepository(Module::class)->find($id); endif;
        if (empty($module)): $module = new Module(); endif;
        if ($request->isMethod('POST')):
            $module->setName($request->get('name'));
            $module->setRender($request->get('render_' . $module->getId()) ?? '');
           
            $new_input = $request->get('input')['new'];
            if (!empty($new_input['name']) && !empty($new_input['type'])): 
                $input = $em->getRepository(Input::class)->find($new_input['type']);
                if ($input):
                    $variable = new Variable();
                    $variable->setName($new_input['name']);
                    $variable->setValue($new_input['value']);
                    $variable->setModule($module);
                    $variable->setInput($input);
                    $em->persist($variable);
                    $module->addVariable($variable);
                    $em->persist($module);
                    $input->addVariable($variable);
                    $em->persist($input);
                endif;
            endif;
            
            foreach ($request->get('inputs') ?? [] as $id => $input):
                $variable = $module->getVariableById($id);
                if ($variable):
                    $variable->setName($input['name']);
                    $type = $em->getRepository(Input::class)->find($input['type']);
                    if ($type): $variable->setInput($type); endif;
                    $variable->setValue($input['value']);
                    $variable->setModule($module);
                    $em->persist($variable);
                endif;
            endforeach;

            $em->persist($module);
            $em->flush();
            return $this->redirectToRoute('module_form', ['id' => $module->getId()]);
        endif;
        
        $inputs = $em->getRepository(Input::class)->findAll();
        return $this->render('admin/module.html.twig', ['module' => $module, 'inputs' => $inputs]);
    }

    public function delete(string $id) {
        $em = $this->getDoctrine()->getManager();
        $module = $em->getRepository(Module::class)->findOneBy(['id' => $id]);
        if (!$module): return $this->redirectToRoute('admin'); endif;

        $em->remove($module);
        $em->flush();

        return $this->redirectToRoute('admin');
    }
}
