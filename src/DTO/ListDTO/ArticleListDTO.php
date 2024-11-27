<?php declare(strict_types=1);

namespace App\DTO\ListDTO;

use App\DTO\ArticleDTO;
use App\Serializer\AccessGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

class ArticleListDTO implements DTOListInterface
{
    #[Property(type: 'array', items: new Items(new Model(type: ArticleDTO::class)))]
    #[Groups([
        AccessGroup::ARTICLE_READ,
    ])]
    /** @var array<int, ArticleDTO> $data */
    public ?array $data = [];

    #[Groups([
        AccessGroup::ARTICLE_READ,
    ])]
    public MetaDTO $meta;
}
