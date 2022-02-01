<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=App\Repository\PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=244)
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $likes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authorId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $replyTo;

        /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $retweet;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrReply;

        /**
     * @ORM\Column(type="integer")
     */
    private $nbrRetweet;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    public function setAuthorId(string $authorId): self
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function getReplyTo(): ?int
    {
        return $this->replyTo;
    }

    public function setReplyTo(?int $replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function getRetweet(): ?int
    {
        return $this->retweet;
    }

    public function setRetweet(?int $retweet): self
    {
        $this->retweet = $retweet;

        return $this;
    }

    public function getNbrReplyTo(): int
    {
        return $this->nbrReply;
    }

    public function setNbrReplyTo(int $nbrReply): self
    {
        $this->nbrReply = $nbrReply;

        return $this;
    }

    public function getNbrRetweet(): int
    {
        return $this->nbrRetweet;
    }

    public function setNbrRetweet(int $nbrRetweet): self
    {
        $this->nbrRetweet = $nbrRetweet;

        return $this;
    }
}
