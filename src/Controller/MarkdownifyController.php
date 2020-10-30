<?php

namespace App\Controller;

use App\Service\Text;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MarkdownifyController extends AbstractController
{
    /**
     * @Route("/markdownify", name="markdownify")
     */
    public function markdownify(Request $request) {
        $text = new Text();
        $markdown = $text->markdownify($request->get('content') ?? '');
        return $this->render('markdownify/_markdown.html.twig', ['markdown' => $markdown]);
    }
}
