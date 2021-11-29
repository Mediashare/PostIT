<?php

namespace App\Service;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Controller\AbstractController;
use App\Entity\Template;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

Class Twig extends AbstractController {
    static public $cache = false;
    static public $debug = true;
    static public $functions = [
        ["method" => "path"],
        ["method" => "url"],
        ["method" => "user"],
        ["method" => "template"],
    ];

    public function __construct(
        UrlGeneratorInterface $router,
        EntityManagerInterface $em,
        Text $text
        ) {
            $this->router = $router;
            $this->em = $em;
            $this->text = $text;
    }

    public function view(string $template, ?array $parameters = []) {
        $loader = new \Twig\Loader\ArrayLoader([
            'index.html' => $template
        ]);
        $twig = new Environment($loader, [
            'cache' => Twig::$cache,
            'debug' => Twig::$debug
        ]);

        if (Twig::$debug):
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        endif;

        foreach (Twig::$functions as $function):
            $twig->addFunction(
                $this->{$function['method']}()
            );
        endforeach;

        return $twig->render('index.html', $parameters);
    }
    
    public function path() {
        return new TwigFunction('path', function (?string $route_name = null, ?array $parameters = []) {
            return $this->router->generate($route_name, $parameters);
        });
    }
    
    public function url() {
        return new TwigFunction('url', function (?string $route_name = null, ?array $parameters = []) {
            return $this->router->generate($route_name, $parameters, UrlGenerator::ABSOLUTE_URL);
        });
    }
    
    public function user() {
        return new TwigFunction('user', function () {
            return $this->getUser();
        });
    }
    
    public function template() {
        return new TwigFunction('template', function (string $template_name) {
            Twig::$debug = true;
            $template =  $this->em->getRepository(Template::class)->findOneBy(['name' => $template_name]);
            if ($template): $content = $template->getContent();
            else: $content = "Template " . $template_name . " not found!"; endif;
            return $this->text->markdownify($this->view($content));
        });
    }
}
