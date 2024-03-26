<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ThreadsUserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ThreadsUserRepository::class)]
class ThreadsUser
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $username;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $lastCheck = null;

    #[ORM\Column]
    private ?bool $federated = false;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $federatedAt = null;

    /**
     * @var Collection<int, Watch>
     */
    #[ORM\ManyToMany(targetEntity: Watch::class, mappedBy: 'watched')]
    private Collection $watchers;

    public function __construct(string $username)
    {
        $this->username = $username;
        $this->watchers = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getLastCheck(): ?DateTimeInterface
    {
        return $this->lastCheck;
    }

    public function setLastCheck(?DateTimeInterface $lastCheck): static
    {
        $this->lastCheck = $lastCheck;

        return $this;
    }

    public function isFederated(): ?bool
    {
        return $this->federated;
    }

    public function setFederated(bool $federated): static
    {
        $this->federated = $federated;

        return $this;
    }

    public function getFederatedAt(): ?DateTimeImmutable
    {
        return $this->federatedAt;
    }

    public function setFederatedAt(?DateTimeImmutable $federatedAt): static
    {
        $this->federatedAt = $federatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Watch>
     */
    public function getWatchers(): Collection
    {
        return $this->watchers;
    }

    public function addWatcher(Watch $watcher): static
    {
        if (!$this->watchers->contains($watcher)) {
            $this->watchers->add($watcher);
            $watcher->addWatched($this);
        }

        return $this;
    }

    public function removeWatcher(Watch $watcher): static
    {
        if ($this->watchers->removeElement($watcher)) {
            $watcher->removeWatched($this);
        }

        return $this;
    }
}
