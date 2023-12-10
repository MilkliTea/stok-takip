<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Controller\StockStorageProductController;
use App\Repository\StockStorageProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockStorageProductRepository::class)]
#[ApiResource(operations: [
    new Post(
        uriTemplate: '/stock-storage-product/update-stock',
        uriVariables: [
            'storageCode', 'productName', 'quantity'
        ],
        controller: StockStorageProductController::class,
        description: 'Stok giriş ve Güncelleme',
        name: 'update-stock',
    ),
    new Get()
]
)]
class StockStorageProduct
{
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ORM\Id]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stockStorageProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(description: 'Ürün adı', jsonSchemaContext: ['type' => 'array',
        'items' => ['name' => 'string',]
    ])]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'stockStorageProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(description: 'Depo adı')]
    private ?Storage $storage = null;

    #[ORM\Column]
    #[ApiProperty(description: 'Stok Adeti')]
    private ?int $quantity = null;


    public function getId(): int
    {
        return $this->id;
    }

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
}
