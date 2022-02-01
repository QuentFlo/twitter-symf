<?php

namespace App\Entity;

use App\Repository\FollowedUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FollowedUserRepository::class)
 */
class FollowedUser
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
    private $userId;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $followed = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getFollowed(): ?array
    {
        return $this->followed;
    }

    public function setFollowed(?array $followed): self
    {
        $this->followed = $followed;

        return $this;
    }
}
