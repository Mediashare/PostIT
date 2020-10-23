<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MarkdownifyController extends AbstractController
{
    /**
     * @Route("/markdownify", name="markdownify")
     */
    public function markdownify(Request $request) {
        $post = new Post();
        $post->setContent($request->get('content') ?? '');
        return $this->render('markdownify/_markdown.html.twig', [
            'markdown' => $post->getMarkdown(),
        ]);
    }
}
