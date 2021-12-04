<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use App\Service\Text;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity=Post::class, mappedBy="article", cascade={"persist", "remove"})
     */
    private $post;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMarkdown(): ?string {
        $text = new Text();
        return $text->markdownify($this->getContent()) ?? '';
    }

    public function getImage(): ?string {
        $crawler = new Crawler($this->getMarkdown());
        if ($crawler->filter('img')->count() > 0):
            $image = $crawler->filter('img')->eq(0);
            $image = $image->attr('src') ?? null;
        endif;
        return $image ?? null;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        // unset the owning side of the relation if necessary
        if ($post === null && $this->post !== null) {
            $this->post->setArticle(null);
        }

        // set the owning side of the relation if necessary
        if ($post !== null && $post->getArticle() !== $this) {
            $post->setArticle($this);
        }

        $this->post = $post;

        return $this;
    }
}
