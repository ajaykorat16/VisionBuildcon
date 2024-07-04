<?php

namespace App\Controller\Admin;

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
        private readonly EntityManagerInterface $entityManager,
        private readonly ImageUploader $imageUploader
    ){
    }

    #[Route('/images', name: 'image_upload', methods: ['POST'], options: ['expose' => true])]
    public function uploadImage(Request $request): JsonResponse
    {
        $files = $request->files->get('file');
        $imageEntities = [];

        if (is_array($files)) {
            
            foreach ($files as $uploadedFile) {
                $imageEntities[] = $this->imageUploader->upload($uploadedFile);
            }
        }else {
            
            $imageEntities[] = $this->imageUploader->upload($files);
        }

        return new JsonResponse(['status' => 'OK','images' =>  $imageEntities], Response::HTTP_OK);
    }

}