<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 */
class Grade
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="grades")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Skill", inversedBy="grades")
     */
    private $skill;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $grades;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    public function getGrades(): ?float
    {
        return $this->grades;
    }

    public function setGrades(?float $grades): self
    {
        $this->grades = $grades;

        return $this;
    }
}
