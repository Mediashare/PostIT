<?php

namespace App\Entity;

use App\Repository\VariableRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VariableRepository::class)
 */
class Variable
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=Input::class, inversedBy="variables")
     */
    private $input;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="variables")
     */
    private $module;

    public function __construct() {
        $this->setId(\uniqid());
    }

    public function setId(string $id): self {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getInput(): ?Input
    {
        return $this->input;
    }

    public function setInput(?Input $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }
}
