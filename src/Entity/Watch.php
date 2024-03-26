<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WatchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WatchRepository::class)]
class Watch
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private string $watcher;

    /**
     * @var Collection<int, ThreadsUser>
     */
    #[ORM\ManyToMany(targetEntity: ThreadsUser::class, inversedBy: 'watchers')]
    private Collection $watched;

    public function __construct(string $watcher)
    {
        $this->watcher = $watcher;
        $this->watched = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getWatcher(): string
    {
        return $this->watcher;
    }

    public function setWatcher(string $watcher): static
    {
        $this->watcher = $watcher;

        return $this;
    }

    /**
     * @return Collection<int, ThreadsUser>
     */
    public function getWatched(): Collection
    {
        return $this->watched;
    }

    public function addWatched(ThreadsUser $watched): static
    {
        if (!$this->watched->contains($watched)) {
            $this->watched->add($watched);
        }

        return $this;
    }

    public function removeWatched(ThreadsUser $watched): static
    {
        $this->watched->removeElement($watched);

        return $this;
    }
}
