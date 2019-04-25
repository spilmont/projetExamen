<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     *
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $usercode;

    /**
     * @ORM\Column(type="integer")
     */
    private $idrank;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Grade", mappedBy="user",cascade={"remove"})
     */
    private $grades;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $class;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="receiver",cascade={"remove"})
     */
    private $receiver;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="sender",cascade={"remove"})
     */
    private $sender;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idProf;





    public function __construct()
    {
        $this->grades = new ArrayCollection();
        $this->receiver = new ArrayCollection();
        $this->sender = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getUsercode(): ?string
    {
        return $this->usercode;
    }

    public function setUsercode(string $usercode): self
    {
        $this->usercode = $usercode;

        return $this;
    }

    public function getIdrank(): ?int
    {
        return $this->idrank;
    }

    public function setIdrank(int $idrank): self
    {
        $this->idrank = $idrank;

        return $this;
    }

    public function getRoles()
    {
        if ($this->idrank == 1)
            return ['ROLE_ADMIN'];
        elseif ($this->idrank == 3)
            return ['ROLE_USER'];
        elseif ($this->idrank == 2)
            return ['ROLE_SUPERADMIN'];
        else
            return [];
    }


    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->lastname,
            $this->firstname,
            $this->usercode,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));

    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->lastname,
            $this->firstname,
            $this->usercode,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
       return $this->usercode;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return Collection|Grade[]
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): self
    {
        if (!$this->grades->contains($grade)) {
            $this->grades[] = $grade;
            $grade->setUser($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): self
    {
        if ($this->grades->contains($grade)) {
            $this->grades->removeElement($grade);
            // set the owning side to null (unless already changed)
            if ($grade->getUser() === $this) {
                $grade->setUser(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string)$this->getId();
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getReceiver(): Collection
    {
        return $this->receiver;
    }

    public function addReceiver(Comments $receiver): self
    {
        if (!$this->receiver->contains($receiver)) {
            $this->receiver[] = $receiver;
            $receiver->setReceiver($this);
        }

        return $this;
    }

    public function removeReceiver(Comments $receiver): self
    {
        if ($this->receiver->contains($receiver)) {
            $this->receiver->removeElement($receiver);
            // set the owning side to null (unless already changed)
            if ($receiver->getReceiver() === $this) {
                $receiver->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getSender(): Collection
    {
        return $this->sender;
    }

    public function addSender(Comments $sender): self
    {
        if (!$this->sender->contains($sender)) {
            $this->sender[] = $sender;
            $sender->setSender($this);
        }

        return $this;
    }

    public function removeSender(Comments $sender): self
    {
        if ($this->sender->contains($sender)) {
            $this->sender->removeElement($sender);
            // set the owning side to null (unless already changed)
            if ($sender->getSender() === $this) {
                $sender->setSender(null);
            }
        }

        return $this;
    }

    public function getIdProf(): ?int
    {
        return $this->idProf;
    }

    public function setIdProf(?int $idProf): self
    {
        $this->idProf = $idProf;

        return $this;
    }
}
