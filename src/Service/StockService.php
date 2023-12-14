<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\StockStorageProduct;
use App\Entity\Storage;
use App\Repository\ProductRepository;
use App\Repository\StockStorageProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class StockService
{
    public function __construct(
        private StockStorageProductRepository $stockStorageProductRepository,
        private ProductRepository $productRepository,
        private StorageService $storageService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function addStock(Storage $storage, Product $product, int $quantity): array
    {
        $stockStorageProduct = $this->stockStorageProductRepository->add($storage, $product, $quantity);

        return $this->prepareData($stockStorageProduct);
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

        return $this->prepareData($stockStorageProduct);
    }

    public function updateStock(Storage $storage, Product $product, int $quantity): array
    {
        $stockStorageProduct = $this->getStockStorageProduct($storage, $product);

        $stockStorageProduct->setQuantity($quantity);

        $this->entityManager->persist($stockStorageProduct);
        $this->entityManager->flush();

        return $this->prepareData($stockStorageProduct);
    }

    public function deleteStock(StockStorageProduct $stockStorageProduct): void
    {
        $this->entityManager->remove($stockStorageProduct);
        $this->entityManager->flush();

        return;
    }

    public function prepareData(StockStorageProduct $stockStorageProduct): array
    {
        return [
            'storage' => [
                'id' => $stockStorageProduct->getStorage()->getId(),
                'name' => $stockStorageProduct->getStorage()->getName(),
                'code' => $stockStorageProduct->getStorage()->getCode(),
            ],
            'product' => [
                'id' => $stockStorageProduct->getProduct()->getId(),
                'name' => $stockStorageProduct->getProduct()->getName(),
                'description' => $stockStorageProduct->getProduct()->getDescription(),
            ],
            'quantity' => $stockStorageProduct->getQuantity(),
        ];
    }

    public function existProductAndStorage(int $storageId, int $productId): array
    {
        $storage = $this->storageService->getStorageById($storageId);

        if (!$storage) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Hatalı Depo Kodu lütfen kontrol ediniz.',
                'storage' => null,
                'product' => null,
            ];
        }

        $product = $this->productRepository->find($productId);

        if (!$product) {
            return [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Ürün Bulunamadı.',
                'storage' => null,
                'product' => null,
            ];
        }

        return [
            'status' => Response::HTTP_FOUND,
            'message' => 'Ürün ve Depo mevcut',
            'storage' => $storage,
            'product' => $product,
        ];
    }

    public function getStockStorageProduct(Storage $storage, Product $product): StockStorageProduct
    {
        return $this->stockStorageProductRepository->findOneBy([
            'storage' => $storage,
            'product' => $product,
        ]);
    }
}
