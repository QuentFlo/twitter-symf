<?php

namespace App\Entity;

use App\Repository\UserInfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInfoRepository::class)
 */
class UserInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $liked;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $followed;

    /**
     * @ORM\Column(type="integer")
     */
    private $followers;

    /** 
     * @ORM\Column(type="integer")
     */
    private $following;

        /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar ;
    public function getUserId(): ?int
    {
        return $this->userId;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
        return $this;
    }

    public function getLiked(): ?string
    {
        return $this->liked;
    }

    public function setLiked(?string $liked): self
    {
        $this->liked = $liked;

        return $this;
    }

    public function getFollowed(): ?string
    {
        return $this->followed;
    }

    public function setFollowed(?string $followed): self
    {
        $this->followed = $followed;

        return $this;
    }

    public function getFollowers(): int
    {
        return $this->followers;
    }

    public function setFollowers(int $followers): self
    {
        $this->followers = $followers;

        return $this;
    }

    public function getFollowing(): int
    {
        return $this->following;
    }

    public function setFollowing(int $following): self
    {
        $this->following = $following;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}
