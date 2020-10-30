<?php

namespace App\Entity;

use App\Service\Text;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PageRepository;

/**
 * @ORM\Entity(repositoryClass=PageRepository::class)
 */
class Page
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Pages")
     */
    private $author;

    public function __toString() {
        return 'Page';
    }

    public function __construct() {
        $this->setId(\uniqid());
        $this->setCreateDate(new \DateTime());
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self {
        $this->id = $id;
        return $this;
    }

    public function getUrl(): ?string {
        return $this->url;
    }

    public function setUrl(string $url): self {
        $text = new Text();
        $url = $text->slugify($url);
        if (\substr($url, 0, 1) !== "/"): $url = "/" . $url; endif;
        $this->url = $url;
        return $this;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): self {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
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

    public function getCreateDate(): ?\DateTime
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTime $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
