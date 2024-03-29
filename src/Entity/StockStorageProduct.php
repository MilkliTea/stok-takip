<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\StockStorageProductController;
use App\Repository\StockStorageProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockStorageProductRepository::class)]
#[ApiResource(
    operations: [
    new Post(
        uriTemplate: '/api/stock',
        controller: StockStorageProductController::class,
        description: 'Stok Giriş',
        name: 'add-stock',
    ),
    new Get(
        uriTemplate: '/api/stock{storageId}/{productId}',
        uriVariables: ['storageId', 'productId'],
        controller: StockStorageProductController::class,
        description: 'Stok Kontrol',
        name: 'check-stock'
    ),
    new Patch(
        uriTemplate: 'api/stock/edit/{storageId}/{productId}',
        uriVariables: ['storageId', 'productId'],
        controller: StockStorageProductController::class,
        description: 'Stok Güncelleme',
        name: 'update-stock',
    ),
    new Delete(
        uriTemplate: '/api/stock/delete{storageId}/{productId}',
        uriVariables: ['storageId', 'productId'],
        controller: StockStorageProductController::class,
        description: 'Depodan ürün siler',
        name: 'delete-stock'
    ),
],
    formats: ['json' => ['application/json']]
)]
class StockStorageProduct
{
    #[ORM\ManyToOne(inversedBy: 'stockStorageProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Id]
    #[ApiProperty(description: 'Ürün adı', readable: false, writable: false, schema: ['type' => 'string', 'example' => 'kalem'])]
    private ?Product $product = null;
    #[ORM\ManyToOne(inversedBy: 'stockStorageProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Id]
    #[ApiProperty(description: 'Depo adı', readable: false, writable: false, schema: ['type' => 'string', 'example' => 'KA1'])]
    private ?Storage $storage = null;

    #[ORM\Column]
    #[ApiProperty(description: 'Stok Adeti', schema: ['type' => 'integer', 'examples' => [5]])]
    private ?int $quantity = null;

    #[ApiProperty(description: 'Ürün id', schema: ['type' => 'integer', 'examples' => [1]])]
    private ?int $productId = null;
    #[ApiProperty(description: 'Depo id', schema: ['type' => 'integer', 'examples' => [3]])]
    private ?int $storageId = null;

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    public function setStorage(?Storage $storage): static
    {
        $this->storage = $storage;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getStorageId(): ?int
    {
        return $this->storageId;
    }
}
