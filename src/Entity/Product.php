<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(description: 'Ürün')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(description: 'Ürün Adı')]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ApiProperty(description: 'Ürün Açıklaması', required: false)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $status = true;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: StockStorageProduct::class)]
    #[ApiProperty(writable: false)]
    private Collection $stockStorageProducts;

    public function __construct()
    {
        $this->stockStorageProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, StockStorageProduct>
     */
    public function getStockStorageProducts(): Collection
    {
        return $this->stockStorageProducts;
    }

    public function addStockStorageProduct(StockStorageProduct $stockStorageProduct): static
    {
        if (!$this->stockStorageProducts->contains($stockStorageProduct)) {
            $this->stockStorageProducts->add($stockStorageProduct);
            $stockStorageProduct->setProduct($this);
        }

        return $this;
    }

    public function removeStockStorageProduct(StockStorageProduct $stockStorageProduct): static
    {
        if ($this->stockStorageProducts->removeElement($stockStorageProduct)) {
            // set the owning side to null (unless already changed)
            if ($stockStorageProduct->getProduct() === $this) {
                $stockStorageProduct->setProduct(null);
            }
        }

        return $this;
    }
}
