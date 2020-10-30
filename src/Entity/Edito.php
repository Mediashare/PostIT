<?php

namespace App\Entity;

use App\Service\Text;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EditoRepository;

/**
 * @ORM\Entity(repositoryClass=EditoRepository::class)
 */
class Edito
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Editos")
     */
    private $author;

    public function __toString() {
        return 'Edito';
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
