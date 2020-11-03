<?php

namespace App\Entity;

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
     * @ORM\Column(type="string")
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
     * @ORM\ManyToMany(targetEntity=Input::class, inversedBy="modules")
     */
    private $inputs;

    /**
     * @ORM\OneToMany(targetEntity=Variable::class, mappedBy="module")
     */
    private $variables;

    public function __construct() {
        $this->setId(\uniqid());
        $this->setAllow(false);
        $this->inputs = new ArrayCollection();
        $this->variables = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self {
        $this->id = $id;
        return $this;
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
     * @return array|Input[]
     */
    public function getInputs(): array {
        foreach ($this->inputs as $input):
            $inputs[$input->getId()] = $input;
        endforeach;
        return $inputs ?? [];
    }

    public function getInput(string $id): ?Input {
        foreach ($this->getInputs() as $input):
            if ($id == $input->getId()): return $input; endif;
        endforeach;
        return null;
    }

    public function addInput(Input $input): self
    {
        if (!$this->inputs->contains($input)) {
            $this->inputs[] = $input;
        }

        return $this;
    }

    public function removeInput(Input $input): self
    {
        $this->inputs->removeElement($input);

        return $this;
    }

    /**
     * @return array|Variable[]
     */
    public function getVariables(): array {
        foreach ($this->variables as $variable):
            $variables[$variable->getName()] = $variable;
        endforeach;
        return $variables ?? [];
    }

    public function getVariableByName(string $name): ?Variable {
        foreach ($this->getVariables() as $variable):
            if ($name == $variable->getName()): return $variable; endif;
        endforeach;
        return null;
    }

    public function getVariableById(string $id): ?Variable {
        foreach ($this->getVariables() as $variable):
            if ($id == $variable->getId()): return $variable; endif;
        endforeach;
        return null;
    }

    public function addVariable(Variable $variable): self
    {
        if (!$this->variables->contains($variable)) {
            $this->variables[] = $variable;
            $variable->setModule($this);
        }

        return $this;
    }

    public function removeVariable(Variable $variable): self
    {
        if ($this->variables->removeElement($variable)) {
            // set the owning side to null (unless already changed)
            if ($variable->getModule() === $this) {
                $variable->setModule(null);
            }
        }

        return $this;
    }
}
