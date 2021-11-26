<?php

namespace App\Entity;

use App\Repository\HorarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=HorarioRepository::class)
 */
class Horario
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     */
    private $fecha;

    /**
     * @ORM\Column(type="time")
     * @Assert\NotBlank
     */
    private $horaEntrada;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $horaSalida;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $horaSaldo;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="horarios")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHoraEntrada(): ?\DateTimeInterface
    {
        return $this->horaEntrada;
    }

    public function setHoraEntrada(\DateTimeInterface $horaEntrada): self
    {
        $this->horaEntrada = $horaEntrada;

        return $this;
    }

    public function getHoraSalida(): ?\DateTimeInterface
    {
        return $this->horaSalida;
    }

    public function setHoraSalida(?\DateTimeInterface $horaSalida): self
    {
        $this->horaSalida = $horaSalida;

        return $this;
    }

    public function getHoraSaldo(): ?\DateTimeInterface
    {
        return $this->horaSaldo;
    }

    public function setHoraSaldo(?\DateTimeInterface $horaSaldo): self
    {
        $this->horaSaldo = $horaSaldo;

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
