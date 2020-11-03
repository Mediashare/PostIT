<?php
namespace App\Controller;

use App\Entity\Page;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Input;
use App\Entity\Module;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PartialController extends AbstractController {
    public function menu(Request $request, ?Post $post, ?Page $page) {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findBy([], ['createDate' => 'DESC']);
        return $this->render('partial/_menu.html.twig', [
            'posts' => $posts,
            'currentPost' => $post,
            'page' => $page,
            'request' => $request
        ]);
    }

    public function signature(?string $username = null) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user):
            if (!$this->getUser()): return new Response(''); endif;
            $user = $this->getUser();
        endif;
        return $this->render('partial/_signature.html.twig', ['user' => $user]);
    }

    public function module(Request $request): Response {
        $id = $request->request->get('id');
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
        return new Response($twig->render('_module.html.twig', ['module' => $module, 'inputs' => $module->getInputs()]), 200);
    }

    public function input(Request $request): Response {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        if ($id): 
            $input = $em->getRepository(Input::class)->find($id);
        endif;
        if (empty($input)): $input = new Input(); endif;
        if ($request->get('module')):
            $module = $em->getRepository(Module::class)->findOneBy(['id' => $request->get('module'), 'input' => $input->getId()]);
        endif;
        $input->setRender($request->get('content') ?? $request->get('render_' + $input->getId()));
        $loader = new \Twig\Loader\ArrayLoader(['_input.html.twig' => $input->getRender()]);
        $twig = new \Twig\Environment($loader, ['debug' => true]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        return new Response($twig->render('_input.html.twig', ['input' => $input, 'module' => $module ?? null]), 200);
    }
}