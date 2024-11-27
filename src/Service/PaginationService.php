<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\ListDTO\MetaDTO;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class PaginationService
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param Request $request
     * @return array{paginator: Paginator, meta: MetaDTO}
     * @throws NotFoundHttpException
     */
    public function handlePagination(QueryBuilder $queryBuilder, Request $request): array
    {
        $itemsPerPage = (int)$request->get('itemsPerPage', 12);

        if ($itemsPerPage <= 0) {
            throw new NotFoundHttpException("Items per page must be a positive integer.");
        }

        $currentPage = max((int)$request->get('page', 1), 1);

        $paginator = new Paginator($queryBuilder, $queryBuilder->getDQLPart('join') !== null);
        $paginator->getQuery()->setFirstResult(($currentPage - 1) * $itemsPerPage)->setMaxResults($itemsPerPage);

        $totalItems = count($paginator);

        $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl().$request->getPathInfo();

        return [
            'paginator' => $paginator,
            'meta' => $this->calculateMetadata($totalItems, $currentPage, $itemsPerPage, $baseUrl),
        ];
    }

    private function calculateMetadata(int $totalCount, int $currentPage, int $itemsPerPage, string $baseUrl): MetaDTO
    {
        $metaDTO = new MetaDTO();
        $metaDTO->totalCount = $totalCount;
        $metaDTO->currentPage = $currentPage;
        $metaDTO->totalPages = (int)ceil($totalCount / $itemsPerPage);
        $metaDTO->itemsPerPage = $itemsPerPage;
        $metaDTO->links = $this->generatePaginationLinks($currentPage, $metaDTO->totalPages, $itemsPerPage, $baseUrl);

        return $metaDTO;
    }

    /**
     * @return array{
     *     first: string,
     *     last: string,
     *     prev: string|null,
     *     next: string|null
     * }
     */
    private function generatePaginationLinks(
        int $currentPage,
        int $totalPages,
        int $itemsPerPage,
        string $baseUrl
    ): array {
        return [
            'first' => $baseUrl.'?page=1&itemsPerPage='.$itemsPerPage,
            'last' => $baseUrl.'?page='.$totalPages.'&itemsPerPage='.$itemsPerPage,
            'prev' => $currentPage > 1 ? $baseUrl.'?page='.($currentPage - 1).'&itemsPerPage='.$itemsPerPage : null,
            'next' => $currentPage < $totalPages ? $baseUrl.'?page='.($currentPage + 1).'&itemsPerPage='.$itemsPerPage : null,
        ];
    }
}
