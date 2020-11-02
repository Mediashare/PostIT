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
     * @ORM\OneToMany(targetEntity=Input::class, mappedBy="module", orphanRemoval=true)
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
    public function getInputs()
    {
        foreach ($this->inputs ?? [] as $input):
            $inputs[$input->getKey()] = $input;
        endforeach;

        return $inputs ?? [];
    }

    public function getInput(string $key): ?Input {
        return $this->getInputs()[$key] ?? null;
    }

    public function addInput(Input $input): self
    {
        if (!$this->inputs->contains($input)) {
            $this->inputs[$input->getId()] = $input;
            $input->setModule($this);
        }

        return $this;
    }

    public function removeInput(Input $input): self
    {
        if ($this->inputs->removeElement($input)) {
            // set the owning side to null (unless already changed)
            if ($input->getModule() === $this) {
                $input->setModule(null);
            }
        }

        return $this;
    }
}
