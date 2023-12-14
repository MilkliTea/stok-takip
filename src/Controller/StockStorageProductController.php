<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\StockService;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StockStorageProductController extends AbstractController
{
    public function __construct(public StockService $stockService, public StorageService $storageService, public ProductRepository $productRepository)
    {
    }

    #[Route('/api/stock', name: 'add-stock', methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $storageId = $requestData['storageId'];
        $productId = $requestData['productId'];

        $storage = $this->storageService->getStorageById($storageId);

        if (!$storage) {
            return new JsonResponse([
                'message' => 'Depo Bulunamadı',
            ], Response::HTTP_NOT_FOUND);
        }

        $product = $this->productRepository->find($productId);

        if (!$product) {
            return new JsonResponse([
                'message' => 'Ürün Bulunamadı.',
            ], Response::HTTP_NOT_FOUND);
        }

        $stockStorageProduct = $this->stockService->addStock(
            $storage,
            $product,
            $requestData['quantity']
        );

        return new JsonResponse(
            $stockStorageProduct,
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/{storageId}/{productId}', name: 'check-stock', methods: 'GET')]
    public function show(int $storageId, int $productId): JsonResponse
    {
        $stockStorageProduct = $this->stockService->getStockStorageProduct($storageId, $productId);

        if (Response::HTTP_NOT_FOUND === $stockStorageProduct['status']) {
            return new JsonResponse([
                'message' => $stockStorageProduct['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $this->stockService->prepareData($stockStorageProduct['data']),
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/edit/{storageId}/{productId}', name: 'update-stock', methods: 'PATCH')]
    public function edit(int $storageId, int $productId, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $stockStorageProduct = $this->stockService->getStockStorageProduct($storageId, $productId);

        if (Response::HTTP_NOT_FOUND === $stockStorageProduct['status']) {
            return new JsonResponse([
                'message' => $stockStorageProduct['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $updatedStockStorageProduct = $this->stockService->updateStock(
            $stockStorageProduct['data'],
            $requestData['quantity']
        );

        return new JsonResponse(
            $updatedStockStorageProduct,
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/{storageId}/{productId}', name: 'delete-stock', methods: 'DELETE')]
    public function delete(int $storageId, int $productId): JsonResponse
    {
        $stockStorageProduct = $this->stockService->getStockStorageProduct($storageId, $productId);

        if (Response::HTTP_NOT_FOUND === $stockStorageProduct['status']) {
            return new JsonResponse([
                'message' => $stockStorageProduct['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $this->stockService->deleteStock($stockStorageProduct['data']);

        return new JsonResponse([
            'message' => 'Ürün Depodan Başarıyla Silindi.',
        ], Response::HTTP_FOUND);
    }
}
