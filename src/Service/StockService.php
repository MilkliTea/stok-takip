<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Storage;
use App\Repository\ProductRepository;
use App\Repository\StockStorageProductRepository;

class StockService
{

    public function __construct(private StockStorageProductRepository $stockStorageProductRepository,
                                private ProductRepository             $productRepository,
    )
    {
    }

    public function addStock(Storage $storage, string $productName, int $quantity): array
    {
        $product = $this->productRepository->getProduct($productName);

        $stockStorageProduct = $this->stockStorageProductRepository->add($storage, $product, $quantity);

        return [
            'product_name' => $stockStorageProduct->getProduct()->getName(),
            'storage_name' => $stockStorageProduct->getStorage()->getName(),
            'quantity' => $stockStorageProduct->getQuantity(),
        ];
    }

    public function checkStock(Storage $storage, Product $product): ?array
    {
        $stockStorageProduct = $this->stockStorageProductRepository->findOneBy([
            'storage' => $storage,
            'product' => $product,
        ]);

        if (!$stockStorageProduct) {
            return null;
        }

        return [
            'product_name' => $stockStorageProduct->getProduct()->getName(),
            'storage_name' => $stockStorageProduct->getStorage()->getName(),
            'quantity' => $stockStorageProduct->getQuantity(),
        ];
    }
}
