<?php

declare(strict_types=1);

namespace App\Entity;

use App\DTO\DTOInterface;

interface EntityInterface
{
    public function getDTO(): DTOInterface;
}
