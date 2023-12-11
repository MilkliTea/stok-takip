<?php

namespace App\Controller;

use App\Repository\StorageRepository;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StorageController extends AbstractController
{
    #[Route('api/storage/{code}', name: 'storage', methods: 'GET')]
    public function index($code, StorageService $storageService): JsonResponse
    {
        $storage = $storageService->getStorageByCode($code);

        if ($storage === null) {
            return new JsonResponse([
                'message' => 'Depo Bulunamadı'
            ],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse([
            'data' => [
                'name' => $storage->getName(),
                'code' => $storage->getCode(),
                'products' => $storage->getProducts()
            ]
        ], Response::HTTP_OK);
    }

    #[Route('api/storages', name: 'storage-list', methods: 'GET')]
    public function list(StorageRepository $storageRepository): JsonResponse
    {
        $storages = array_map(function ($storage) {
            return [
                'id' => $storage->getId(),
                'name' => $storage->getName(),
                'code' => $storage->getCode(),
                'products' => $storage->getProducts()
            ];
        }, $storageRepository->findAll());

        return new JsonResponse(['data' => $storages]);
    }


}
