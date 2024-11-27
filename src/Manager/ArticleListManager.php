<?php declare(strict_types=1);

namespace App\Manager;

use App\DTO\ListDTO\ArticleListDTO;
use App\Entity\Article;
use App\Serializer\AccessGroup;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Random\RandomException;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;

final readonly class ArticleListManager
{
    use AutoMapper;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginationService $paginationService,
    ) {
    }

    /**
     * @throws ReflectionException
     * @throws RandomException
     * @throws Exception
     */
    public function getListFilter(Request $request): ArticleListDTO
    {
        $queryBuilder = $this->getQbWithFilter($request);

        $paginationResult = $this->paginationService->handlePagination($queryBuilder, $request);
        $paginator = $paginationResult['paginator'];
        $articles = iterator_to_array($paginator->getIterator());
        $listDto = new ArticleListDTO();
        $listDto->meta = $paginationResult['meta'];

        foreach ($articles as $article) {
            $listDto->data[] = $this->mapToModel($article, AccessGroup::ARTICLE_READ);
        }

        $this->entityManager->flush();

        return $listDto;
    }

    private function getQbWithFilter(Request $request): QueryBuilder
    {
        $queryBuilder = $this->entityManager->getRepository(Article::class)->createQueryBuilder('article');

        $this->applySearchFilter($queryBuilder, $request);
        $this->applySorting($queryBuilder, $request);

        return $queryBuilder;
    }

    private function applySearchFilter(QueryBuilder $queryBuilder, Request $request): void
    {
        $searchTerm = $request->get('search');
        if (null !== $searchTerm) {
            $this->joinUser($queryBuilder);
            $queryBuilder
                ->andWhere('article.title LIKE :searchTerm OR article.content LIKE :searchTerm OR author.firstName LIKE :searchTerm OR author.lastName LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
    }

    private function applySorting(QueryBuilder $queryBuilder, Request $request): void
    {
        $sortField = $request->get('sort');
        $sortOrder = $request->get('order', 'asc');
        $validFields = [
            'id',
            'title',
            'content',
        ];
        $validDirections = ['asc', 'desc'];
        if (!in_array($sortOrder, $validDirections, true)) {
            $sortOrder = 'asc';
        }

        if (in_array($sortField, $validFields, true)) {
            $queryBuilder->orderBy('article.' . $sortField, strtoupper($sortOrder));
        } else {
            $queryBuilder->orderBy('article.id', 'ASC');
        }
    }

    private function joinUser(QueryBuilder $queryBuilder): void
    {
        if (!in_array('au', $queryBuilder->getAllAliases(), true)) {
            $queryBuilder->leftJoin('article.author', 'author');
        }
    }
}
