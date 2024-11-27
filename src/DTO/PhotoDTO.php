<?php declare(strict_types=1);

namespace App\DTO;

use App\Entity\EntityInterface;
use App\Entity\Photo;
use App\Serializer\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

class PhotoDTO implements DTOInterface
{
    #[Groups([
        AccessGroup::ARTICLE_CREATE,
        AccessGroup::ARTICLE_READ,
        AccessGroup::PHOTO_READ
    ])]
    public ?int $id;

    #[Groups([
        AccessGroup::PHOTO_READ,
        AccessGroup::ARTICLE_READ,
    ])]
    public string $filename;

    #[Groups([
        AccessGroup::PHOTO_READ,
        AccessGroup::ARTICLE_READ,
    ])]
    public string $path;

    #[Groups([
        AccessGroup::PHOTO_READ,
        AccessGroup::ARTICLE_READ,
    ])]
    public string $mimeType;

    #[Ignore] public function getEntityObject(): EntityInterface
    {
        return new Photo();
    }
}
