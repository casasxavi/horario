<?php

namespace App\Entity;

use App\Repository\AgendaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AgendaRepository::class)
 */
class Agenda
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telefono_fijo;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telefono_movil;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="agendas")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getTelefonoFijo(): ?string
    {
        return $this->telefono_fijo;
    }

    public function setTelefonoFijo(?string $telefono_fijo): self
    {
        $this->telefono_fijo = $telefono_fijo;

        return $this;
    }

    public function getTelefonoMovil(): ?string
    {
        return $this->telefono_movil;
    }

    public function setTelefonoMovil(?string $telefono_movil): self
    {
        $this->telefono_movil = $telefono_movil;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
