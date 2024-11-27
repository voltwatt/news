<?php declare(strict_types=1);

namespace App\DTO\ListDTO;

class EmptyListDTO implements DTOListInterface
{
    public ?array $data = [];

    public MetaDTO $meta;
}
