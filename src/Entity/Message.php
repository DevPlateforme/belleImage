<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Chat::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Sender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Receiver;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    public function setChat(?Chat $chat): self
    {
        $this->chat = $chat;

        return $this;
    }

    public function getSender(): ?string
    {
        return $this->Sender;
    }

    public function setSender(string $Sender): self
    {
        $this->Sender = $Sender;

        return $this;
    }

    public function getReceiver(): ?string
    {
        return $this->Receiver;
    }

    public function setReceiver(string $Receiver): self
    {
        $this->Receiver = $Receiver;

        return $this;
    }
}
