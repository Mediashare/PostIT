<?php
namespace App\Service;

use Parsedown;
use Cocur\Slugify\Slugify;

Class Text {
    public function Markdownify (?string $text): ?string {
        $parsedown = new Parsedown();
        // $parsedown->setSafeMode(true);
        $markdown = $parsedown->text($text);
        $markdown = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $markdown);
        $markdown = preg_replace('#<script(.*?)>(.*?)</script(.*?)>#is', '', $markdown);
        $markdown = preg_replace('/<script\b[^>]*>/is', "", $markdown);
        // $markdown = \htmlspecialchars_decode($markdown);
        return $markdown ?? '';
    }

    public function Slugify (?string $text): ?string {
        $slugify = new Slugify();
        $slug = $slugify->slugify($text);
        return $slug ?? '';
    }
}