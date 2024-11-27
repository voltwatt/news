<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\ErrorResponseDTO;
use App\DTO\UserDTO;
use App\Manager\UserManager;
use App\Serializer\AccessGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final  class UserController extends AbstractController
{
    public function __construct(private readonly UserManager $userManager)
    {
    }

    #[Post(
        summary: 'Create user',
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(type: UserDTO::class, groups: [AccessGroup::USER_SIGN])
            )
        ),
        tags: ['User'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_CREATED,
                description: 'Successful response',
                content: [new Model(type: UserDTO::class, groups: [AccessGroup::USER_READ])]
            ),
            new Response(
                response: HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation Error',
                content: [new Model(type: ErrorResponseDTO::class)]
            ),
        ]
    )]
    #[Route('/users', name: 'app_user_create', methods: [Request::METHOD_POST])]
    public function create(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => [AccessGroup::USER_SIGN],
            ],
            validationGroups: [AccessGroup::USER_SIGN]
        )]
        UserDTO $userDTO
    ): JsonResponse {
        return $this->json($this->userManager->create($userDTO), HttpResponse::HTTP_CREATED);
    }
}
