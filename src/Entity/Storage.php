<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Tests\Fixtures\Metadata\Get;
use App\Controller\StorageController;
use App\Repository\StorageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
#[ApiResource(description: 'Depo oluşturmak için kullanılır.', operations: [
    new Get(
        uriTemplate: '/storage/{code}',
        uriVariables: 'code',
        controller: StorageController::class,
        name: 'storage',
    ),
    new Post(),
    new Patch(),
    new GetCollection(
        uriTemplate: '/storages',
        controller: StorageController::class,
        name: 'storage-list',
    ),
    new Delete(),
], formats: ['json' => ['application/json']]
)]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'storage', targetEntity: StockStorageProduct::class)]
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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
            $stockStorageProduct->setStorage($this);
        }

        return $this;
    }

    public function removeStockStorageProduct(StockStorageProduct $stockStorageProduct): static
    {
        if ($this->stockStorageProducts->removeElement($stockStorageProduct)) {
            // set the owning side to null (unless already changed)
            if ($stockStorageProduct->getStorage() === $this) {
                $stockStorageProduct->setStorage(null);
            }
        }

        return $this;
    }

    public function getProducts()
    {
        return array_map(function ($storageProduct) {
            return [
                'name' => $storageProduct->getProduct()->getName(),
                'quantity' => $storageProduct->getQuantity()
            ];
        }, $this->getStockStorageProducts()->toArray());
    }
}
