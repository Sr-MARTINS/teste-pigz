<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ListasRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ListasRepository::class)]
class Listas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('user')]
    private ?int $id = null;

    #[Assert\Length(
        min: 3,
        max: 150,
        minMessage: 'O campo deve ter no minimo: 3 caracters',
        maxMessage: 'O campo deve ter no maximo: 50 caracters',
    )]
    #[Groups('user')]
    #[ORM\Column(length: 150)]
    private ?string $titulo = null;

    #[ORM\ManyToOne(inversedBy: 'listas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $usuario = null;

    #[Assert\Type(type: 'bool', message: 'O valor deve ser verdadeiro ou falso (true ou false).')]
    #[Groups('user')]
    #[ORM\Column(type: 'boolean')]
    private bool $is_publico = false;

     #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    

    /**
     * @var Collection<int, Tarefas>
     */
    #[ORM\OneToMany(targetEntity: Tarefas::class, mappedBy: 'lista', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups('user')]
    private Collection $tarefas;

    public function __construct()
    {
        $this->tarefas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getUsuario(): ?Usuarios
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuarios $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function isPublico(): bool
    {
        return $this->is_publico;
    }

    public function setIsPublico(bool $isPublico): static
    {
        $this->is_publico = $isPublico;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

 

    /**
     * @return Collection<int, Tarefas>
     */
    public function getTarefas(): Collection
    {
        return $this->tarefas;
    }

    public function addTarefa(Tarefas $tarefa): static
    {
        if (!$this->tarefas->contains($tarefa)) {
            $this->tarefas->add($tarefa);
            $tarefa->setListaId($this);
        }

        return $this;
    }

    public function removeTarefa(Tarefas $tarefa): static
    {
        if ($this->tarefas->removeElement($tarefa)) {
            if ($tarefa->getListaId() === $this) {
                $tarefa->setListaId(null);
            }
        }

        return $this;
    }
}
