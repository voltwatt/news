<?php declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

final readonly class ErrorResponseDTO
{
    public function __construct(
        #[OA\Property(description: 'HTTP status code')]
        #[Groups(['error'])]
        public int $status,

        #[OA\Property(description: 'Error message')]
        #[Groups(['error'])]
        public string $message,

        #[OA\Property(description: 'Error type/code')]
        #[Groups(['error'])]
        public string $type = 'error',

        #[OA\Property(description: 'Detailed error information', type: 'object', nullable: true)]
        #[Groups(['error'])]
        public ?array $details = null,
    ) {
    }
}
