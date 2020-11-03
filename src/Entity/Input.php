<?php

namespace App\Entity;

use App\Repository\InputRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InputRepository::class)
 */
class Input
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $render;

    /**
     * @ORM\ManyToMany(targetEntity=Module::class, mappedBy="inputs")
     */
    private $modules;

    /**
     * @ORM\OneToMany(targetEntity=Variable::class, mappedBy="input")
     */
    private $variables;

    public function __construct() {
        $this->setId(\uniqid());
        $this->modules = new ArrayCollection();
        $this->variables = new ArrayCollection();
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(string $id): self {
        $this->id = $id;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(string $type): self {
        $this->type = $type;

        return $this;
    }

    public function getRender(): ?string {
        return $this->render;
    }

    public function setRender(string $render): self {
        $this->render = $render;

        return $this;
    }

    /**
     * @return Collection|Module[]
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->addInput($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->removeElement($module)) {
            $module->removeInput($this);
        }

        return $this;
    }

    /**
     * @return Collection|Variable[]
     */
    public function getVariables(): Collection
    {
        return $this->variables;
    }

    public function addVariable(Variable $variable): self
    {
        if (!$this->variables->contains($variable)) {
            $this->variables[] = $variable;
            $variable->setInput($this);
        }

        return $this;
    }

    public function removeVariable(Variable $variable): self
    {
        if ($this->variables->removeElement($variable)) {
            // set the owning side to null (unless already changed)
            if ($variable->getInput() === $this) {
                $variable->setInput(null);
            }
        }

        return $this;
    }
}
