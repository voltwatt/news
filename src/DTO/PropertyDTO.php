<?php declare(strict_types=1);

namespace App\DTO;

class PropertyDTO
{
    public string $name;
    public string $type;
    public bool $dto = false;
    public ?object $transform = null;
    public bool $translatable = false;
}
