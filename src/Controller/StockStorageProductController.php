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

        $productAndStorageData = $this->stockService->existProductAndStorage($requestData['storageId'], $requestData['productId']);

        if (Response::HTTP_NOT_FOUND === $productAndStorageData['status']) {
            return new JsonResponse([
                'message' => $productAndStorageData['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $result = $this->stockService->addStock(
            $productAndStorageData['storage'],
            $productAndStorageData['product'],
            $requestData['quantity']
        );

        return new JsonResponse(
            $result,
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/{storageId}/{productId}', name: 'check-stock', methods: 'GET')]
    public function show(int $storageId, int $productId): JsonResponse
    {
        $productAndStorageData = $this->stockService->existProductAndStorage($storageId, $productId);

        if (Response::HTTP_NOT_FOUND === $productAndStorageData['status']) {
            return new JsonResponse([
                'message' => $productAndStorageData['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $stockStorageProduct = $this->stockService->checkStock(
            $productAndStorageData['storage'],
            $productAndStorageData['product']
        );

        if (null === $stockStorageProduct) {
            return new JsonResponse([
                'message' => 'Depoda Ürün Bulunamadı.',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $stockStorageProduct,
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/edit/{storageId}/{productId}', name: 'update-stock', methods: 'PATCH')]
    public function edit(int $storageId, int $productId, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $productAndStorageData = $this->stockService->existProductAndStorage($storageId, $productId);

        if (Response::HTTP_NOT_FOUND === $productAndStorageData['status']) {
            return new JsonResponse([
                'message' => $productAndStorageData['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $stockStorageProduct = $this->stockService->updateStock(
            $productAndStorageData['storage'],
            $productAndStorageData['product'],
            $requestData['quantity']
        );

        return new JsonResponse(
            $stockStorageProduct,
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/stock/{storageId}/{productId}', name: 'delete-stock', methods: 'DELETE')]
    public function delete(int $storageId, int $productId): JsonResponse
    {
        $productAndStorageData = $this->stockService->existProductAndStorage($storageId, $productId);

        if (Response::HTTP_NOT_FOUND === $productAndStorageData['status']) {
            return new JsonResponse([
                'message' => $productAndStorageData['message'],
            ], Response::HTTP_NOT_FOUND);
        }

        $stockStorageProduct = $this->stockService->getStockStorageProduct(
            $productAndStorageData['storage'],
            $productAndStorageData['product']
        );

        $this->stockService->deleteStock($stockStorageProduct);

        return new JsonResponse(
            'Ürün Depodan Başarıyla Silindi.',
            Response::HTTP_OK
        );
    }
}
