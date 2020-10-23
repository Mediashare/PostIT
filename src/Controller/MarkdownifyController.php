<?php

namespace App\Controller;

use Parsedown;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MarkdownifyController extends AbstractController
{
    /**
     * @Route("/markdownify", name="markdownify")
     */
    public function markdownify(Request $request) {
        $parsedown = new Parsedown();
        // $parsedown->setSafeMode(true);
        $markdown = $parsedown->text($request->get('content') ?? "");
        $markdown = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $markdown);
        $markdown = preg_replace('#<script(.*?)>(.*?)</script(.*?)>#is', '', $markdown);
        $markdown = preg_replace('/<script\b[^>]*>/is', "", $markdown);
        $markdown = \htmlspecialchars_decode($markdown);
        return $this->render('markdownify/_markdown.html.twig', [
            'markdown' => $markdown,
        ]);
    }
}
