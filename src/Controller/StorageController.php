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
    #[Route('api/storages', name: 'api_storages_index', methods: 'GET')]
    public function index(StorageRepository $storageRepository): JsonResponse
    {
        $storages = array_map(function ($storage) {
            return [
                'id' => $storage->getId(),
                'name' => $storage->getName(),
                'code' => $storage->getCode(),
                'products' => $storage->getProducts(),
            ];
        }, $storageRepository->findAll());

        return new JsonResponse(
            $storages,
            Response::HTTP_OK
        );
    }

    #[Route('api/storage/{id}', name: 'api_storage_show', methods: 'GET')]
    public function show(int $id, StorageService $storageService): JsonResponse
    {
        $storage = $storageService->getStorageById($id);

        if (null === $storage) {
            return new JsonResponse([
                'message' => 'Depo Bulunamadı',
            ],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse([
                'name' => $storage->getName(),
                'code' => $storage->getCode(),
                'products' => $storage->getProducts(),
        ], Response::HTTP_OK);
    }
}
