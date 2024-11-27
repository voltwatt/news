<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\ErrorResponseDTO;
use App\DTO\PhotoDTO;
use App\Serializer\AccessGroup;
use App\Service\PhotoUploadService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Attributes\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Property;

final class PhotoUploadController extends AbstractController
{
    public function __construct(
        private readonly PhotoUploadService $photoUploadService,
    ) {
    }

    #[Post(
        summary: 'Upload new file',
        requestBody: new RequestBody(
            content: [
                new MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new Schema(
                        properties: [
                            new Property(
                                property: 'file',
                                description: 'file to upload',
                                type: 'string',
                                format: 'binary',
                            ),
                        ]
                    )
                ),
            ]
        ),
        tags: ['File'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_CREATED,
                description: 'Successful response',
                content: [new Model(type: PhotoDTO::class, groups: [AccessGroup::PHOTO_READ])]
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad Request',
                content: [new Model(type: ErrorResponseDTO::class)]
            ),
        ]
    )]
    #[Route('/photo/upload', name: 'app_photo_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        return $this->json($this->photoUploadService->upload($file));
    }
}
