<?php

namespace App\Controller;

use App\Entity\Input;
use App\Entity\Module;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleController extends AbstractController {
    public function module(Request $request, ?int $id = null): Response {
        $em = $this->getDoctrine()->getManager();
        if ($id): $module = $em->getRepository(Module::class)->find($id); endif;
        if (empty($module)): $module = new Module(); endif;
        if ($request->isMethod('POST')):
            $module->setName($request->get('name'));
            $module->setRender($request->get('render'));
            $input = $request->get('input')['new'];
            if (!empty($input['key']) && !empty($input['value']) && !empty($input['type'])): 
                $input = new Input($input['key'], $input['value'], $input['type']);
                $input->setModule($module);
                $em->persist($input);
            endif;
            
            foreach ($module->getInputs() ?? [] as $key => $input):
                $input->setKey($request->get('inputs')[$key]['key']);
                $input->setType($request->get('inputs')[$key]['type']);
                $input->setValue($request->get('inputs')[$key]['value']);
                $em->persist($input);
            endforeach;
            
            $em->persist($module);
            $em->flush();
            return $this->redirectToRoute('module_form', ['id' => $module->getId()]);
        endif;
        return $this->render('admin/module_form.html.twig', ['module' => $module]);
    }

    public function show(Request $request, ?int $id = null): Response {
        if ($id): 
            $module = $this->getDoctrine()->getManager()->getRepository(Module::class)->find($id);
        endif;
        if (empty($module)): 
            $module = new Module(); 
        endif;
        $module->setRender($request->get('content'));
        $loader = new \Twig\Loader\ArrayLoader(['_module.html.twig' => $module->getRender()]);
        $twig = new \Twig\Environment($loader, ['debug' => false]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        return new Response($twig->render('_module.html.twig', ['module' => $module, 'inputs' => $module->getInputs()]), 200);
    }
}
