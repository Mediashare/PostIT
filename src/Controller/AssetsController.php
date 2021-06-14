<?php

namespace App\Controller;

use App\Controller\AbstractController;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use Symfony\Component\HttpFoundation\Response;

class AssetsController extends AbstractController {
    public function css() {
        $webpath = $this->getParameter('kernel.project_dir') . '/public/';
        $files = ['font/recursive.css', 'css/bootstrap.min.css', 'css/retro.css', 'css/menu.css', 'css/custom.css', 'fontawesome/css/all.min.css', 'css/prism.css'];
        $minifier = new CSS();
        foreach ($files as $file):
            $minifier->add($webpath.$file);
        endforeach;
        return new Response($minifier->minify(), 200, ['Content-Type' => 'text/css']);
    }
    public function js() {
        $webpath = $this->getParameter('kernel.project_dir') . '/public/';
        $files = ['js/jquery.min.js', 'js/bootstrap.min.js', 'js/app.js'];
        $minifier = new JS();
        foreach ($files as $file):
            $minifier->add($webpath.$file);
        endforeach;
        return new Response($minifier->minify(), 200, ['Content-Type' => 'text/javascript']);
    }
}
