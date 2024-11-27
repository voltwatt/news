<?php declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes\Property;

class TokenDTO
{
    #[Property(example: 'eyJ0e.....MampjZlOjc')]
    public ?string $token;

    #[Property(example: 'fe837e130.....6ce7e54466400e6')]
    public ?string $refreshToken;
}
