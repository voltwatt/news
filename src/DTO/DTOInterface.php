<?php declare(strict_types=1);

namespace App\DTO;

use App\Entity\EntityInterface;
use Symfony\Component\Serializer\Attribute\Ignore;

interface DTOInterface
{
    #[Ignore]
    public function getEntityObject(): EntityInterface;
}
