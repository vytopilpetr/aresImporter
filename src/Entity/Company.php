<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", name="company_id")
     */
    private int $companyId;

    /**
     * @ORM\Column(type="string", length=250)
     */
    private ?string $name = null;

    /**
     * @ORM\OneToOne(targetEntity="Branch", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     */
    private ?Branch $branch = null;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private ?DateTime $createdAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function setBranch(?Branch $branch): void
    {
        $this->branch = $branch;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /** @PrePersist */
    public function doStuffOnPrePersist(): void
    {
        $this->createdAt = new DateTime();
    }

}