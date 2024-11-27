<?php

namespace App\DTO;

use App\Entity\Article;
use App\Entity\EntityInterface;
use App\Serializer\AccessGroup;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

class ArticleDTO implements DTOInterface
{
    #[Groups([
        AccessGroup::ARTICLE_READ,
    ])]
    public ?int $id;

    #[Groups([
        AccessGroup::ARTICLE_READ,
        AccessGroup::ARTICLE_CREATE,
    ])]
    #[Property(example: 'Title Article')]
    public string $title;

    #[Groups([
        AccessGroup::ARTICLE_READ,
        AccessGroup::ARTICLE_CREATE,
    ])]
    public UserDTO $author;

    #[Groups([
        AccessGroup::ARTICLE_READ,
        AccessGroup::ARTICLE_CREATE,
    ])]
    #[Property(example: 'Content Article')]
    public string $content;

    #[Groups([
        AccessGroup::ARTICLE_READ,
        AccessGroup::ARTICLE_CREATE,
    ])]
    public PhotoDTO $photo;

    #[Ignore]
    public function getEntityObject(): EntityInterface
    {
        return new Article();
    }
}
