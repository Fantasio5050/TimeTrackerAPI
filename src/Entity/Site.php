<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
#[ApiResource]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domain = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, SessionSite>
     */
    #[ORM\OneToMany(targetEntity: SessionSite::class, mappedBy: 'site_id')]
    private Collection $sessionSites;

    public function __construct()
    {
        $this->sessionSites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, SessionSite>
     */
    public function getSessionSites(): Collection
    {
        return $this->sessionSites;
    }

    public function addSessionSite(SessionSite $sessionSite): static
    {
        if (!$this->sessionSites->contains($sessionSite)) {
            $this->sessionSites->add($sessionSite);
            $sessionSite->setSiteId($this);
        }

        return $this;
    }

    public function removeSessionSite(SessionSite $sessionSite): static
    {
        if ($this->sessionSites->removeElement($sessionSite)) {
            // set the owning side to null (unless already changed)
            if ($sessionSite->getSiteId() === $this) {
                $sessionSite->setSiteId(null);
            }
        }

        return $this;
    }
}
