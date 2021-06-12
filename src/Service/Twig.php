<?php

namespace App\Service;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

Class Twig {
    static public $cache = false;
    static public $debug = true;
    static public $functions = [
        ["method" => "path"],
        ["method" => "url"],
    ];

    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    public function view(string $template, ?array $parameters = []) {
        // $loader = new FilesystemLoader(__DIR__.'/../../templates');
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

        // $template = $twig->load($template);
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
}
