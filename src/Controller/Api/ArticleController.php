<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\ArticleDTO;
use App\DTO\ErrorResponseDTO;
use App\Entity\Article;
use App\Manager\ArticleListManager;
use App\Manager\ArticleManager;
use App\Serializer\AccessGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly ArticleListManager $articleListManager,
    ) {
    }

    #[Get(
        summary: 'Returns list of articles',
        tags: ['Article'],
        parameters: [
            new Parameter(
                name: 'page',
                description: 'The collection page number',
                in: 'query',
                schema: new Schema(type: 'integer', default: 1)
            ),
            new Parameter(
                name: 'itemsPerPage',
                description: 'The number of items per page',
                in: 'query',
                schema: new Schema(type: 'integer', default: 12)
            ),
            new Parameter(
                name: 'search',
                description: 'Search by fields: title, content, author',
                in: 'query'
            ),
            new Parameter(
                name: 'sort',
                description: 'Field to sort by',
                in: 'query',
                required: false,
                schema: new Schema(
                    type: 'string',
                    default: 'id',
                    enum: [
                        'id',
                        'title',
                        'content',
                    ]
                )
            ),
            new Parameter(
                name: 'order',
                description: 'Order to sort by, ascending (asc) or descending (desc)',
                in: 'query',
                required: false,
                schema: new Schema(
                    type: 'string',
                    default: 'asc',
                    enum: ['asc', 'desc']
                )
            ),
        ],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Successful response',
                content: [new Model(type: ArticleDTO::class, groups: [AccessGroup::ARTICLE_READ])]
            ),
        ]
    )]
    #[Route('/articles', name: 'app_list_articles', methods: [Request::METHOD_GET])]
    public function getListArticles(Request $request): JsonResponse
    {
        $articles = $this->articleListManager->getListFilter($request);

        return $this->json($articles, HttpResponse::HTTP_OK);
    }

    #[Post(
        summary: 'Create article',
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(type: ArticleDTO::class, groups: [AccessGroup::ARTICLE_CREATE])
            )
        ),
        tags: ['Article'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_CREATED,
                description: 'Successful response',
                content: [new Model(type: ArticleDTO::class, groups: [AccessGroup::ARTICLE_READ])]
            ),
            new Response(
                response: HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation Error',
                content: [new Model(type: ErrorResponseDTO::class)]
            ),
        ]
    )]
    #[Route('/articles', name: 'app_article_create', methods: [Request::METHOD_POST])]
    public function create(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => [AccessGroup::ARTICLE_CREATE],
            ],
            validationGroups: [AccessGroup::ARTICLE_CREATE]
        )]
        ArticleDTO $articleDTO
    ): JsonResponse {
        return $this->json($this->articleManager->create($articleDTO), HttpResponse::HTTP_CREATED);
    }

    #[Delete(
        summary: 'Delete article',
        tags: ['Article'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_NO_CONTENT,
                description: 'Successful response',
                content: []
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad Request',
                content: [new Model(type: ErrorResponseDTO::class)]
            ),
        ]
    )]
    #[Route('/articles/{id}', name: 'app_article_delete', methods: [Request::METHOD_DELETE])]
    public function delete(
        Article $article,
    ): JsonResponse {
        $this->articleManager->remove($article);

        return new JsonResponse(null, HttpResponse::HTTP_NO_CONTENT);
    }
}
