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
            foreach ($request->get('inputs') ?? [] as $key => $input):
                if (!empty($input['key']) && !empty($input['value']) && !empty($input['type'])): 
                    $input = new Input($input['key'], $input['value'], $input['type']);
                    $input->addModule($module);
                    $em->persist($input);
                endif;
            endforeach;
            $em->persist($module);
            $em->flush();
            return $this->redirectToRoute('admin');
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
        $twig = new \Twig\Environment($loader, ['debug' => true]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        return new Response($twig->render('_module.html.twig', ['module' => $module]));
    }
}
