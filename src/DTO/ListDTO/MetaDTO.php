<?php declare(strict_types=1);

namespace App\DTO\ListDTO;

use App\Serializer\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;

#[Groups([
    AccessGroup::ARTICLE_READ,
])]
class MetaDTO
{
    public int $totalCount;

    public int $currentPage;

    public int $totalPages;

    public int $itemsPerPage;

    public array $links = [];
}
