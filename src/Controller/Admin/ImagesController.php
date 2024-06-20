<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Repository\ImagesRepository;
use App\Uploader\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImagesController extends AbstractController
{
    public function __construct(
        private readonly ImagesRepository $imagesRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }
    #[Route('/images', name: 'image_upload', methods: ['POST'], options: ['expose' => true])]
    public function uploadImage(Request $request, ImageUploader $imageUploader): JsonResponse
    {
        $files = $request->files->get('file');
        $imageEntities = [];

        if ($files) {
            foreach ($files as $uploadedFile) {
                $newFileName = $imageUploader->upload($uploadedFile);

                $image = new Images();
                $image->setImage($newFileName);
                $imageEntities[] = $image;
            }
        }

        $response = [
            'success' => 'Files uploaded successfully',
            'images' => array_map(function($image) {
                return [
                    'path' => $image->getImage(),
                ];
            }, $imageEntities)
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/remove-image/{imageId}', name: 'project_remove_image', options: ['expose' => true ])]
    public function removeImage(int $imageId): JsonResponse
    {
        $image = $this->imagesRepository->find($imageId);

        if ($image) {
            unlink('image/'. $image->getImage());
            $this->entityManager->remove($image);
            $this->entityManager->flush();
            return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Image not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/logo', name: 'upload_logo',  methods: ['POST'], options: ['expose' => true])]
    public function uploadAllImage(Request $request, ImageUploader $imageUploader): JsonResponse
    {
        $file = $request->files->get('file');
        if ($file) {
            $newFileName = $imageUploader->upload($file);
            return new JsonResponse(['fileName' => $newFileName]);
        }
        return new JsonResponse(['error' => 'No file uploaded'], 400);
    }

}