<?php

namespace App\Entity;

use App\Repository\FollowRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FollowRepository::class)
 */
class Follow
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user1Id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user2Id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser1Id(): ?int
    {
        return $this->user1Id;
    }

    public function setUser1Id(int $user1Id): self
    {
        $this->user1Id = $user1Id;

        return $this;
    }

    public function getUser2Id(): ?int
    {
        return $this->user2Id;
    }

    public function setUser2Id(int $user2Id): self
    {
        $this->user2Id = $user2Id;

        return $this;
    }

}
