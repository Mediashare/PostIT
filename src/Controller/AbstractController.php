<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;

class AbstractController extends Controller {
    public function getFileContent(UploadedFile $file): ?string {
        return file_get_contents($file->getPathname());
    }
}
