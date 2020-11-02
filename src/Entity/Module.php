<?php

namespace App\Entity;

use App\Entity\Input;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ModuleRepository::class)
 */
class Module
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $render;

    /**
     * @ORM\Column(type="boolean")
     */
    private $allow;

    /**
     * @ORM\ManyToMany(targetEntity=Input::class, inversedBy="modules")
     */
    private $inputs;

    public function __construct() {
        $this->setAllow(false);
        $this->inputs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRender(): ?string
    {
        return $this->render;
    }

    public function setRender(string $render): self
    {
        $this->render = $render;

        return $this;
    }

    public function getAllow(): ?bool
    {
        return $this->allow;
    }

    public function setAllow(bool $allow): self
    {
        $this->allow = $allow;

        return $this;
    }

    /**
     * @return Collection|Input[]
     */
    public function getInputs(): Collection
    {
        return $this->inputs;
    }

    public function addInput(Input $input): self
    {
        if (!$this->inputs->contains($input)) {
            $this->inputs[$input->getKey()] = $input;
        }

        return $this;
    }

    public function removeInput(Input $input): self
    {
        $this->inputs->removeElement($input);

        return $this;
    }
}
